/**
 * BonBon Loader Helper
 * ─────────────────────
 * A reusable loading screen that can be shown inline (small) or fullscreen.
 *
 * Usage:
 *   BonBonLoader.show('#my-container', 'Loading...');   // inline in element
 *   BonBonLoader.show(document.body, 'Please wait...');  // fullscreen
 *   BonBonLoader.showFullscreen('Processing...');        // shortcut for fullscreen
 *   BonBonLoader.hide('#my-container');                  // hide from element
 *   BonBonLoader.hideAll();                              // hide all loaders
 */
window.BonBonLoader = (() => {
    const LOADER_CLASS = 'bb-loader-overlay';
    const LOADER_ATTR = 'data-bb-loader';

    function cakeSvg(small = false) {
        const s = small ? 32 : 48;
        const vs = small ? 36 : 54;
        return `
        <svg class="bb-loader-cake" viewBox="0 0 64 72" fill="none" xmlns="http://www.w3.org/2000/svg" width="${s}" height="${vs}">
            <path class="bb-steam bb-steam-1" d="M28 8 C28 4, 32 2, 32 0" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.6"/>
            <path class="bb-steam bb-steam-2" d="M36 10 C36 6, 40 4, 40 2" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.4"/>
            <rect x="29" y="12" width="4" height="14" rx="2" fill="#F8E2E7"/>
            <ellipse cx="31" cy="11" rx="3" ry="4" fill="#FFD97D"/>
            <ellipse cx="31" cy="12" rx="2" ry="2.5" fill="#FFB347"/>
            <path d="M12 30 C12 30, 16 24, 22 26 C28 28, 30 22, 32 22 C34 22, 36 28, 42 26 C48 24, 52 30, 52 30 L52 38 L12 38 Z" fill="#C88A92"/>
            <rect x="12" y="34" width="40" height="12" rx="3" fill="#F5E6E8"/>
            <rect x="12" y="34" width="40" height="4" rx="2" fill="#E6B7BE" opacity="0.5"/>
            <path d="M8 46 C8 44, 12 42, 18 44 C24 46, 26 40, 32 40 C38 40, 40 46, 46 44 C52 42, 56 44, 56 46 L56 48 L8 48 Z" fill="#C88A92"/>
            <rect x="8" y="46" width="48" height="14" rx="4" fill="#F5E6E8"/>
            <rect x="8" y="46" width="48" height="4" rx="2" fill="#E6B7BE" opacity="0.4"/>
            <ellipse cx="32" cy="62" rx="28" ry="4" fill="#EED9DE"/>
            <circle cx="20" cy="37" r="1.2" fill="#FFB6C1"/>
            <circle cx="28" cy="36" r="1" fill="#FFD97D"/>
            <circle cx="36" cy="37" r="1.2" fill="#FFB6C1"/>
            <circle cx="44" cy="36" r="1" fill="#FFD97D"/>
            <circle cx="31" cy="22" r="4" fill="#E74C6F"/>
            <circle cx="29.5" cy="20.5" r="1.2" fill="#FF7E9D" opacity="0.7"/>
            <path d="M31 18 C33 14, 35 16, 34 18" stroke="#5A3A3A" stroke-width="1" fill="none" stroke-linecap="round"/>
        </svg>`;
    }

    function orbitDots(small = false) {
        const r = small ? 24 : 44;
        return `<div class="bb-orbit" style="--bb-orbit-r:${r}px">
            <span class="bb-dot" style="--i:0"></span>
            <span class="bb-dot" style="--i:1"></span>
            <span class="bb-dot" style="--i:2"></span>
            <span class="bb-dot" style="--i:3"></span>
            <span class="bb-dot" style="--i:4"></span>
            <span class="bb-dot" style="--i:5"></span>
        </div>`;
    }

    function createOverlay(text, isFullscreen, isSmall) {
        const el = document.createElement('div');
        el.className = LOADER_CLASS + (isFullscreen ? ' bb-loader-fullscreen' : '') + (isSmall ? ' bb-loader-small' : '');
        el.setAttribute(LOADER_ATTR, '');

        const small = isSmall;
        el.innerHTML = `
            <div class="bb-loader-inner">
                <div class="bb-loader-anim">
                    ${orbitDots(small)}
                    ${cakeSvg(small)}
                </div>
                ${text ? `<p class="bb-loader-text${small ? ' bb-loader-text-sm' : ''}">${text}</p>` : ''}
            </div>
        `;
        return el;
    }

    function resolve(target) {
        if (typeof target === 'string') return document.querySelector(target);
        return target;
    }

    return {
        /**
         * Show loader inside a target element (inline).
         * @param {string|Element} target - CSS selector or DOM element
         * @param {string} text - Optional loading text
         */
        show(target, text = '') {
            const el = resolve(target);
            if (!el) return;
            this.hide(target); // remove existing
            const isSmall = el.offsetHeight < 200 || el.offsetWidth < 300;
            const overlay = createOverlay(text, false, isSmall);
            el.style.position = el.style.position || 'relative';
            if (!el.style.position || el.style.position === 'static') el.style.position = 'relative';
            el.appendChild(overlay);
        },

        /**
         * Show fullscreen loader overlay.
         * @param {string} text - Optional loading text
         */
        showFullscreen(text = '') {
            this.hide(document.body);
            const overlay = createOverlay(text, true, false);
            document.body.appendChild(overlay);
        },

        /**
         * Hide loader from a target element.
         * @param {string|Element} target - CSS selector or DOM element
         */
        hide(target) {
            const el = resolve(target);
            if (!el) return;
            const existing = el.querySelectorAll(':scope > .' + LOADER_CLASS);
            existing.forEach(o => {
                o.classList.add('bb-loader-fadeout');
                setTimeout(() => o.remove(), 300);
            });
        },

        /**
         * Hide all loaders everywhere.
         */
        hideAll() {
            document.querySelectorAll('.' + LOADER_CLASS).forEach(o => {
                o.classList.add('bb-loader-fadeout');
                setTimeout(() => o.remove(), 300);
            });
        }
    };
})();
