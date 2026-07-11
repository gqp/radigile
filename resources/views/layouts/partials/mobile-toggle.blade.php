{{-- In normal document flow (so it stacks above any per-page navbar rather
     than floating over it). No elevated z-index needed: this button only
     opens the sidebar, and closing happens via the ✕ injected into the
     sidebar itself (which should render on top of this bar, not under it)
     or by tapping outside it. --}}
<div class="d-md-none d-flex justify-content-start p-2" style="background: #fff;">
    <a href="#" data-widget="pushmenu" id="mobileSidebarToggle" role="button" aria-label="Open navigation menu"
       class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-bars"></i>
    </a>
</div>

{{-- Tap-outside-to-close backdrop — AdminLTE's own overlay isn't reliably
     injected in this layout's markup, so we provide our own. --}}
<div id="mobileSidebarOverlay" class="d-md-none" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1037;"></div>

<script>
    (function () {
        const body = document.body;
        const overlay = document.getElementById('mobileSidebarOverlay');
        const sidebar = document.querySelector('.main-sidebar');

        // Inject a transparent ✕ inside the sidebar itself to close it —
        // added here (rather than to every sidebar Blade partial) so all
        // three layouts pick it up from one place.
        let closeBtn = document.getElementById('mobileSidebarClose');
        if (!closeBtn && sidebar) {
            closeBtn = document.createElement('a');
            closeBtn.id = 'mobileSidebarClose';
            closeBtn.href = '#';
            closeBtn.setAttribute('role', 'button');
            closeBtn.setAttribute('aria-label', 'Close navigation menu');
            closeBtn.className = 'd-md-none';
            closeBtn.style.cssText = 'position:absolute; top:10px; right:10px; z-index:10; ' +
                'background:transparent; border:0; color:#fff; opacity:.85; font-size:1.25rem; line-height:1;';
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('mobileSidebarToggle').click();
            });
            sidebar.prepend(closeBtn);
        }

        function sync() {
            overlay.style.display = body.classList.contains('sidebar-open') ? 'block' : 'none';
        }

        new MutationObserver(sync).observe(body, { attributes: true, attributeFilter: ['class'] });
        sync();

        overlay.addEventListener('click', function () {
            document.getElementById('mobileSidebarToggle').click();
        });
    })();
</script>
