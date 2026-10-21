/* =========================================================
   resources/js/app.js
   MAIN JS ENTRY POINT
   ---------------------------------------------------------
   Loaded by Vite via @vite('resources/js/app.js')
   ========================================================= */

/* Bootstrap 5 bundle (includes Popper for dropdowns/tooltips) */
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.js';

/* Make Bootstrap available globally for inline page scripts */
window.bootstrap = bootstrap;

import Chart from 'chart.js/auto';

window.Chart = Chart;

/* =========================================================
   1. COLLAPSIBLE SIDEBAR
   Toggles .is-collapsed on the sidebar and
   .is-sidebar-collapsed on the main area.
   ========================================================= */

function initSidebar() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainWrapper = document.querySelector('.main-wrapper');

    if (!toggle || !sidebar || !mainWrapper) {
        return;
    }

    /* Create the dark overlay once for mobile */
    let backdrop = document.querySelector('.sidebar-backdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
    }

    const isMobile = () => window.innerWidth < 992;

    /* Restore previous desktop collapsed state */
    const saved = sessionStorage.getItem('sidebarCollapsed');
    if (saved === 'true' && !isMobile()) {
        sidebar.classList.add('is-collapsed');
        mainWrapper.classList.add('is-sidebar-collapsed');
    }

    toggle.addEventListener('click', () => {
        if (isMobile()) {
            const open = sidebar.classList.contains('is-mobile-open');
            sidebar.classList.toggle('is-mobile-open', !open);
            backdrop.classList.toggle('is-visible', !open);
        } else {
            const collapsed = !sidebar.classList.contains('is-collapsed');
            sidebar.classList.toggle('is-collapsed', collapsed);
            mainWrapper.classList.toggle('is-sidebar-collapsed', collapsed);
            backdrop.classList.remove('is-visible');
            sessionStorage.setItem('sidebarCollapsed', String(collapsed));
        }
    });

    /* Click the overlay to close the sidebar on mobile */
    backdrop.addEventListener('click', () => {
        sidebar.classList.remove('is-mobile-open');
        backdrop.classList.remove('is-visible');
    });

    /* Close the sidebar after following a menu link on mobile (exclude toggles) */
    sidebar.querySelectorAll('.sidebar-item[href], .sidebar-subitem[href]').forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.remove('is-mobile-open');
                backdrop.classList.remove('is-visible');
            }
        });
    });

    /* Handle window resize gracefully */
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('is-mobile-open');
            backdrop.classList.remove('is-visible');
            const wasCollapsed = sessionStorage.getItem('sidebarCollapsed') === 'true';
            sidebar.classList.toggle('is-collapsed', wasCollapsed);
            mainWrapper.classList.toggle('is-sidebar-collapsed', wasCollapsed);
        } else {
            sidebar.classList.remove('is-collapsed');
            mainWrapper.classList.remove('is-sidebar-collapsed');
        }
    });
}


/* =========================================================
   2. FLASH MESSAGES
   Closes Bootstrap alerts when the X button is clicked and
   auto-hides them after a delay.
   ========================================================= */

function initFlashMessages() {
    document.querySelectorAll('.flash-alert[data-auto-dismiss]').forEach((el) => {
        const delay = parseInt(el.dataset.autoDismiss, 10) || 5000;

        /* Manual close via the X button */
        const closeBtn = el.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => el.remove());
        }

        /* Auto close */
        setTimeout(() => {
            el.style.transition = 'opacity .3s ease';
            el.style.opacity = '0';

            setTimeout(() => el.remove(), 300);
        }, delay);
    });
}


/* =========================================================
   3. ACTIVE SIDEBAR LINK
   Highlights the menu item matching the current URL. Longer
   prefixes win, so /admin/staff/12/edit highlights the staff
   entry rather than the dashboard.
   ========================================================= */

function initActiveSidebarLink() {
    const normalise = (path) => path.replace(/\/+$/, '') || '/';
    const currentPath = normalise(window.location.pathname);

    let best = null;

    document.querySelectorAll('.sidebar-item[href], .sidebar-subitem[href]').forEach((link) => {
        const linkPath = normalise(new URL(link.href).pathname);

        if (linkPath === '#' || !currentPath.startsWith(linkPath)) {
            return;
        }

        if (!best || linkPath.length > best.path.length) {
            best = { link, path: linkPath };
        }
    });

    if (!best) {
        return;
    }

    document.querySelectorAll('.sidebar-item.active, .sidebar-subitem.active')
        .forEach((el) => el.classList.remove('active'));

    best.link.classList.add('active');

    /* Auto-expand parent dropdown if inside a submenu */
    const parentCollapse = best.link.closest('.sidebar-submenu');
    if (parentCollapse) {
        parentCollapse.classList.add('show');
        const toggle = parentCollapse.previousElementSibling;
        if (toggle && toggle.classList.contains('sidebar-dropdown-toggle')) {
            toggle.classList.add('has-active');
            toggle.setAttribute('aria-expanded', 'true');
        }
    }
}


/* =========================================================
   BOOT
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initFlashMessages();
    initActiveSidebarLink();
});