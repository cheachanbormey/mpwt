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

    /* Create the dark overlay once */
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    const setCollapsed = (collapsed) => {
        sidebar.classList.toggle('is-collapsed', collapsed);
        mainWrapper.classList.toggle('is-sidebar-collapsed', collapsed);
        backdrop.classList.toggle('is-visible', collapsed);
    };

    /* Restore previous state */
    const saved = sessionStorage.getItem('sidebarCollapsed');
    if (saved === 'true') {
        setCollapsed(true);
    }

    toggle.addEventListener('click', () => {
        const collapsed = !sidebar.classList.contains('is-collapsed');

        setCollapsed(collapsed);
        sessionStorage.setItem('sidebarCollapsed', String(collapsed));
    });

    /* Click the overlay to close the sidebar */
    backdrop.addEventListener('click', () => {
        setCollapsed(false);
        sessionStorage.setItem('sidebarCollapsed', 'false');
    });

    /* Close the sidebar after following a menu link (mobile) */
    sidebar.querySelectorAll('.sidebar-item').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                setCollapsed(false);
                sessionStorage.setItem('sidebarCollapsed', 'false');
            }
        });
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

    document.querySelectorAll('.sidebar-item[href]').forEach((link) => {
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

    document.querySelectorAll('.sidebar-item.active')
        .forEach((el) => el.classList.remove('active'));

    best.link.classList.add('active');
}


/* =========================================================
   BOOT
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initFlashMessages();
    initActiveSidebarLink();
});