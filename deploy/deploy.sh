#!/usr/bin/env bash
# Reference deploy script for the production Droplet. Adapt paths/user as
# needed and wire it into however you actually deploy (git pull + ssh, a
# CI runner, etc.) — this isn't invoked automatically by anything.
set -euo pipefail

cd /var/www/radigile

php artisan down --retry=5

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force

# Config/route/view caching — see performance audit finding #4. These must be
# cleared before re-caching or Laravel will serve a stale merge of old+new
# config, and must be re-run on every deploy or you're back to uncached.
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Long-lived queue workers (see deploy/README.md) cache the old code in
# memory — restart them so they pick up this deploy.
php artisan queue:restart

# Required if deploy/php/performance.ini's opcache.validate_timestamps=0 is
# in use — OPcache otherwise keeps serving the pre-deploy compiled code
# indefinitely. Harmless no-op if that setting isn't applied.
sudo systemctl reload php8.2-fpm

php artisan up
