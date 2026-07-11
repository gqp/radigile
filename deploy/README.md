# Production Droplet hardening

Reference material for the self-managed DigitalOcean Droplet running
production. Nothing in this directory is applied automatically — review
and adapt each piece, then apply it on the server yourself.

## Files here

- `nginx/radigile.conf` — server block: forces HTTPS, serves only
  `public/`, blocks `.env`/`.git`/`storage`/`bootstrap/cache` from ever
  being served directly, adds security headers as a backup for anything
  that doesn't reach PHP, caches static assets.
  Apply: adapt the domain/socket path, drop into
  `/etc/nginx/sites-available/`, symlink into `sites-enabled/`,
  `nginx -t && systemctl reload nginx`.

- `php/hardening.ini` — PHP-FPM overrides: turns off `expose_php` and
  `display_errors`, disables a set of functions the app doesn't need
  (`exec`, `shell_exec`, `system`, etc.), sets upload limits and
  session-cookie flags.
  Apply: adjust the PHP version path, drop into
  `/etc/php/8.2/fpm/conf.d/99-radigile-hardening.ini`,
  `systemctl restart php8.2-fpm`.

- `deploy.sh` — reference deploy script: pulls code, installs deps, builds
  assets, migrates, re-caches config/routes/views, restarts the queue
  worker and reloads PHP-FPM. Adapt the path/remote details for how you
  actually deploy.

- `php/performance.ini` — OPcache tuning (the single biggest PHP-level
  performance lever available — without it, every file is recompiled from
  scratch on every request). Apply the same way as `hardening.ini`, into
  its own conf.d file. Uses `validate_timestamps=0` for speed, which means
  a deploy needs an explicit PHP-FPM reload to take effect — `deploy.sh`
  already does this.

## Not file-based — do these directly on the server

**Session/cache: move off MySQL onto Redis.** `SESSION_DRIVER` and
`CACHE_STORE` are both `database` right now — every request does a DB
round-trip just for session handling, competing with real app queries for
DB connections. Neither is installed locally in this dev environment either
(confirmed: no `redis-server` running, connection refused), so this can only
be verified once it's actually set up somewhere. To move to Redis:

```bash
sudo apt install redis-server
sudo systemctl enable --now redis-server
```

The PHP `redis` extension may already be present (check `php -m | grep redis`);
if not, `sudo apt install php8.2-redis` (matching your PHP version) or
`composer require predis/predis` as a pure-PHP fallback that needs no
extension. Then in `.env`:

```
SESSION_DRIVER=redis
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

No application code changes needed — `config/session.php` and
`config/cache.php` already read these via env vars.

**MySQL: stop using `root` as the app's database user.** The current
`.env` pattern (`DB_USERNAME=root`) means a compromise of the app gets
full instance-wide MySQL privileges — `DROP DATABASE`, `GRANT`, access
to every other schema on the box — not just this app's data. Create a
scoped user instead:

```sql
CREATE USER 'radigile_app'@'localhost' IDENTIFIED BY '<generate a long random password>';
GRANT SELECT, INSERT, UPDATE, DELETE ON radigiledb.* TO 'radigile_app'@'localhost';
FLUSH PRIVILEGES;
```

Then update production's `DB_USERNAME`/`DB_PASSWORD` to match and
restart PHP-FPM. This user deliberately can't `DROP`/`ALTER`/`GRANT` —
run migrations with a separate, more privileged connection (e.g. `root`
used only interactively for `php artisan migrate`, never stored in the
app's own `.env`).

**Firewall:** only 22 (SSH, ideally key-only + non-standard port or
fail2ban), 80, and 443 should be reachable from the public internet.
MySQL (3306) should not be — confirm with `ufw status` /
`doctl compute firewall list` that the DB port isn't open externally.

**`.env` on the box:** confirm `APP_ENV=production` and
`APP_DEBUG=false` are actually set there — those two are what suppress
Laravel's own debug/stack-trace pages. Also confirm `STRIPE_WEBHOOK_SECRET`
is set (the `/stripe/webhook` route now hard-rejects every request,
including legitimate ones, until it is).

**Deploys:** run `php artisan config:cache`, `route:cache`, and
`view:cache` as part of the deploy step (not required, but standard
practice and closes a class of accidental info-disclosure from
uncached config reads under load).

**Queue worker — required, not optional, as of this change.** `QUEUE_CONNECTION`
is now `database` instead of `sync` (invite/registration emails are queued via
`->queue()`/`ShouldQueue` instead of blocking the request on Mailgun). Queued
jobs just sit in the `jobs` table until something processes them — **with no
worker running, every invite/registration email silently never sends.** Before
deploying this change:

1. Run the new migration if not already applied: `php artisan migrate` (the
   `jobs`/`failed_jobs` tables ship with Laravel's default migrations).
2. Run a persistent worker via supervisor (or systemd) — don't just run
   `php artisan queue:work` in a terminal, it'll die on logout/deploy:

   ```ini
   ; /etc/supervisor/conf.d/radigile-worker.conf
   [program:radigile-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/radigile/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   numprocs=1
   user=www-data
   redirect_stderr=true
   stdout_logfile=/var/www/radigile/storage/logs/worker.log
   ```

   `supervisorctl reread && supervisorctl update && supervisorctl start radigile-worker:*`

3. Every deploy that changes code the worker touches needs
   `supervisorctl restart radigile-worker:*` (or `queue:restart` after the
   next job finishes) — a long-lived worker process caches the old code in
   memory otherwise.
