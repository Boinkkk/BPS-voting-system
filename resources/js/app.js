import Swal from 'sweetalert2';
window.Swal = Swal;

import TomSelect from 'tom-select';
window.TomSelect = TomSelect;

import Cropper from 'cropperjs';
window.Cropper = Cropper;

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

// ─────────────────────────────────────────────────────────────
// Livewire SPA Navigation Skeleton Loader
// Runs from app.js so it persists across wire:navigate swaps.
// ─────────────────────────────────────────────────────────────
function showSkeleton() {
    const el = document.getElementById('page-skeleton');
    if (el) el.classList.add('active');
}

function hideSkeleton() {
    const el = document.getElementById('page-skeleton');
    if (el) el.classList.remove('active');
}

// Livewire v3 / v4 events
document.addEventListener('livewire:navigating', showSkeleton);
document.addEventListener('livewire:navigated', hideSkeleton);

// Fallback: intercept clicks on wire:navigate links directly
document.addEventListener('click', function (e) {
    const link = e.target.closest('a[wire\\:navigate]');
    if (link && link.href && link.href !== window.location.href) {
        showSkeleton();
    }
});

// ─────────────────────────────────────────────────────────────
// Sidebar Active State Manager
// Since <aside wire:persist="sidebar"> is frozen across
// wire:navigate swaps, JS must update active classes after each
// navigation. PHP only renders the correct state on first load.
// ─────────────────────────────────────────────────────────────

/**
 * Routes whose inactive class uses text-gray-700 (general/public pages).
 * All other routes use text-gray-600 (admin/kepala/pegawai pages).
 */
const GRAY700_PREFIXES = ['/dashboard', '/kalender', '/glosarium', '/faq'];

function getInactiveClasses(linkPathname) {
    const isGray700 = GRAY700_PREFIXES.some(
        prefix => linkPathname === prefix || linkPathname.startsWith(prefix + '/')
    );
    if (isGray700) {
        return ['text-gray-700', 'hover:bg-gray-100', 'hover:text-gray-900'];
    }
    return ['text-gray-600', 'hover:bg-bps-bg', 'hover:text-gray-900'];
}

function updateSidebarActiveState() {
    const currentPath = window.location.pathname;
    // Only target links inside <nav> inside #sidebar — excludes profile/panduan/logout
    const links = document.querySelectorAll('#sidebar nav a[href]');

    links.forEach(link => {
        let linkPath;
        try {
            linkPath = new URL(link.href, window.location.origin).pathname;
        } catch {
            return; // skip invalid hrefs
        }

        // Skip empty or root paths to avoid false matches
        if (!linkPath || linkPath === '/') return;

        const isActive = currentPath === linkPath
            || currentPath.startsWith(linkPath + '/')
            || currentPath.startsWith(linkPath + '?');

        const inactiveClasses = getInactiveClasses(linkPath);

        if (isActive) {
            // Remove all possible inactive class variants then activate
            ['text-gray-700', 'text-gray-600',
             'hover:bg-gray-100', 'hover:bg-bps-bg', 'hover:text-gray-900'
            ].forEach(cls => link.classList.remove(cls));
            link.classList.add('sidebar-link-active');
        } else {
            link.classList.remove('sidebar-link-active');
            inactiveClasses.forEach(cls => link.classList.add(cls));
        }
    });
}

// Run on every Livewire navigation completion
document.addEventListener('livewire:navigated', updateSidebarActiveState);
// Run on first hard load to sync state (in case PHP and JS differ)
document.addEventListener('DOMContentLoaded', updateSidebarActiveState);

// ─────────────────────────────────────────────────────────────
// Sidebar Lock Logic
// Using event delegation so it survives any DOM morphing or
// Livewire wire:navigate events automatically.
// ─────────────────────────────────────────────────────────────
function updateSidebarLockState() {
    const sidebar = document.getElementById('sidebar');
    const lockBtn = document.getElementById('sidebarLockBtn');
    const lockIcon = document.getElementById('lockIcon');
    const unlockIcon = document.getElementById('unlockIcon');
    
    if (!sidebar || !lockBtn || !lockIcon || !unlockIcon) return;
    
    if (localStorage.getItem('sidebarLocked') === 'true') {
        sidebar.classList.add('is-locked');
        lockIcon.classList.remove('hidden');
        unlockIcon.classList.add('hidden');
        lockBtn.classList.add('opacity-100', 'text-red-500', 'hover:text-red-600', 'hover:bg-red-50');
        lockBtn.classList.remove('text-gray-400', 'hover:text-blue-600', 'hover:bg-blue-50');
    } else {
        sidebar.classList.remove('is-locked');
        lockIcon.classList.add('hidden');
        unlockIcon.classList.remove('hidden');
        lockBtn.classList.remove('opacity-100', 'text-red-500', 'hover:text-red-600', 'hover:bg-red-50');
        lockBtn.classList.add('text-gray-400', 'hover:text-blue-600', 'hover:bg-blue-50');
    }
}

// Initial sync
document.addEventListener('DOMContentLoaded', updateSidebarLockState);
document.addEventListener('livewire:navigated', updateSidebarLockState);

// Global click delegation for the lock button
document.addEventListener('click', function(e) {
    const lockBtn = e.target.closest('#sidebarLockBtn');
    if (lockBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        let isLocked = localStorage.getItem('sidebarLocked') === 'true';
        localStorage.setItem('sidebarLocked', !isLocked);
        
        updateSidebarLockState();
    }
});
