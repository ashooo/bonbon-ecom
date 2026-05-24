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
    const LOADER_CLASS = "bb-loader-overlay";
    const LOADER_ATTR = "data-bb-loader";
    const AUTO_LOADER_ATTR = "data-bb-auto-loader";
    const SHOW_DELAY_MS = 220;
    const MIN_VISIBLE_MS = 0;

    let activeGlobalLoads = 0;
    let globalShowTimer = null;
    let globalShownAt = 0;

    function getTheme() {
        try {
            const saved = localStorage.getItem("bonbon-loader-theme");
            if (["baking", "classic"].includes(saved)) return saved;
        } catch (_) {}

        const configured =
            window.BONBON_LOADER_THEME ||
            document.documentElement.dataset.loaderTheme;
        return configured === "classic" ? "classic" : "baking";
    }

    function bakingCakeSvg(small = true) {
        const size = small ? 82 : 128;
        return `
        <svg class="bb-baking-scene" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" aria-hidden="true">
            <ellipse class="bb-baking-shadow" cx="80" cy="139" rx="48" ry="8" fill="#E8C9CE" opacity="0.5"/>

            <g class="bb-oven">
                <rect x="34" y="48" width="92" height="76" rx="18" fill="#FFFDFB" stroke="#E9C8CF" stroke-width="4"/>
                <rect x="44" y="64" width="72" height="46" rx="10" fill="#6B4548"/>
                <rect class="bb-oven-glow" x="49" y="69" width="62" height="36" rx="8" fill="#FFD6A7"/>
                <circle cx="52" cy="57" r="4" fill="#E6B7BE"/>
                <circle cx="66" cy="57" r="4" fill="#C88A92"/>
                <path d="M77 57H108" stroke="#EBD5D9" stroke-width="5" stroke-linecap="round"/>
            </g>

            <g class="bb-cake-rise">
                <ellipse cx="80" cy="107" rx="31" ry="7" fill="#C89473"/>
                <rect x="49" y="80" width="62" height="27" rx="9" fill="#F2C78D"/>
                <path d="M49 82C56 75 64 82 70 78C76 74 82 74 88 78C94 82 103 75 111 82V90H49V82Z" fill="#FFF1F4"/>
                <path class="bb-icing-drip bb-icing-drip-1" d="M61 86V99" stroke="#FFF1F4" stroke-width="7" stroke-linecap="round"/>
                <path class="bb-icing-drip bb-icing-drip-2" d="M84 86V101" stroke="#FFF1F4" stroke-width="7" stroke-linecap="round"/>
                <path class="bb-icing-drip bb-icing-drip-3" d="M101 86V96" stroke="#FFF1F4" stroke-width="7" stroke-linecap="round"/>
                <circle class="bb-sprinkle bb-sprinkle-1" cx="63" cy="83" r="2.2" fill="#F06292"/>
                <circle class="bb-sprinkle bb-sprinkle-2" cx="78" cy="80" r="2" fill="#FFD166"/>
                <circle class="bb-sprinkle bb-sprinkle-3" cx="94" cy="83" r="2" fill="#8ED8B8"/>
                <path d="M80 65C78 60 82 57 80 52" stroke="#F5A7B5" stroke-width="3" stroke-linecap="round" class="bb-steam bb-steam-1"/>
                <path d="M66 68C63 63 68 60 65 55" stroke="#E6B7BE" stroke-width="3" stroke-linecap="round" class="bb-steam bb-steam-2"/>
                <path d="M96 68C99 63 94 60 97 55" stroke="#E6B7BE" stroke-width="3" stroke-linecap="round" class="bb-steam bb-steam-3"/>
            </g>

            <g class="bb-whisk">
                <path d="M36 31L54 49" stroke="#C88A92" stroke-width="4" stroke-linecap="round"/>
                <path d="M57 52C51 57 43 57 38 52C33 47 33 39 38 34C43 29 51 29 56 34C61 39 62 47 57 52Z" stroke="#E6B7BE" stroke-width="3"/>
            </g>
        </svg>`;
    }

    function bakingSparkles() {
        return `<div class="bb-baking-sparkles" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
        </div>`;
    }

    function classicCakeSvg(small = false) {
        const s = small ? 32 : 48;
        const vs = small ? 36 : 54;
        return `
        <svg class="bb-loader-cake" viewBox="0 0 64 72" fill="none" xmlns="http://www.w3.org/2000/svg" width="${s}" height="${vs}" aria-hidden="true">
            <path class="bb-classic-steam bb-classic-steam-1" d="M28 8 C28 4,32 2,32 0" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.5"/>
            <path class="bb-classic-steam bb-classic-steam-2" d="M36 10 C36 6,40 4,40 2" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.3"/>
            <rect x="29" y="12" width="4" height="14" rx="2" fill="#F8E2E7"/>
            <ellipse cx="31" cy="11" rx="3" ry="4" fill="#FFD97D"/>
            <ellipse cx="31" cy="12" rx="2" ry="2.5" fill="#FFB347"/>
            <path d="M12 30 C12 30,16 24,22 26 C28 28,30 22,32 22 C34 22,36 28,42 26 C48 24,52 30,52 30 L52 38 L12 38 Z" fill="#C88A92"/>
            <rect x="12" y="34" width="40" height="12" rx="3" fill="#F5E6E8"/>
            <rect x="12" y="34" width="40" height="4" rx="2" fill="#E6B7BE" opacity="0.5"/>
            <path d="M8 46 C8 44,12 42,18 44 C24 46,26 40,32 40 C38 40,40 46,46 44 C52 42,56 44,56 46 L56 48 L8 48 Z" fill="#C88A92"/>
            <rect x="8" y="46" width="48" height="14" rx="4" fill="#F5E6E8"/>
            <rect x="8" y="46" width="48" height="4" rx="2" fill="#E6B7BE" opacity="0.4"/>
            <ellipse cx="32" cy="62" rx="28" ry="4" fill="#EED9DE"/>
            <circle cx="20" cy="37" r="1.2" fill="#FFB6C1"/><circle cx="28" cy="36" r="1" fill="#FFD97D"/>
            <circle cx="36" cy="37" r="1.2" fill="#FFB6C1"/><circle cx="44" cy="36" r="1" fill="#FFD97D"/>
            <circle cx="16" cy="52" r="1.2" fill="#FFD97D"/><circle cx="24" cy="53" r="1" fill="#FFB6C1"/>
            <circle cx="32" cy="51" r="1.3" fill="#B5EAD7"/><circle cx="40" cy="53" r="1" fill="#FFB6C1"/>
            <circle cx="48" cy="52" r="1.2" fill="#FFD97D"/>
            <circle cx="31" cy="22" r="4" fill="#E74C6F"/>
            <circle cx="29.5" cy="20.5" r="1.2" fill="#FF7E9D" opacity="0.7"/>
            <path d="M31 18 C33 14,35 16,34 18" stroke="#5A3A3A" stroke-width="1" fill="none" stroke-linecap="round"/>
        </svg>`;
    }

    function classicOrbitDots(small = false) {
        const r = small ? 24 : 44;
        return `<div class="bb-orbit" style="--bb-orbit-r:${r}px" aria-hidden="true">
            <span class="bb-dot"></span><span class="bb-dot"></span><span class="bb-dot"></span>
            <span class="bb-dot"></span><span class="bb-dot"></span><span class="bb-dot"></span>
        </div>`;
    }

    function createOverlay(text, isFullscreen, isSmall) {
        const el = document.createElement("div");
        el.className =
            LOADER_CLASS +
            (isFullscreen ? " bb-loader-fullscreen" : "") +
            (isSmall ? " bb-loader-small" : "");
        el.setAttribute(LOADER_ATTR, "");
        const theme = getTheme();
        el.setAttribute("data-bb-loader-theme", theme);

        const small = isSmall;
        const animation =
            theme === "classic"
                ? `${classicOrbitDots(small)}${classicCakeSvg(small)}`
                : `${bakingSparkles()}${bakingCakeSvg(small)}`;
        el.innerHTML = `
            <div class="bb-loader-inner">
                <div class="bb-loader-anim">
                    ${animation}
                </div>
                ${text ? `<p class="bb-loader-text${small ? " bb-loader-text-sm" : ""}">${text}</p>` : ""}
            </div>
        `;
        return el;
    }

    function resolve(target) {
        if (typeof target === "string") return document.querySelector(target);
        return target;
    }

    function ensureGlobalLoader(text = "Loading...") {
        const existing = document.querySelector(
            `.${LOADER_CLASS}.bb-loader-fullscreen`,
        );
        if (existing) {
            const textEl = existing.querySelector(".bb-loader-text");
            if (textEl && text) textEl.textContent = text;
            return existing;
        }

        const overlay = createOverlay(text, true, false);
        overlay.setAttribute(AUTO_LOADER_ATTR, "");
        document.body.appendChild(overlay);
        globalShownAt = Date.now();
        return overlay;
    }

    function beginGlobalLoad(text = "Loading...") {
        activeGlobalLoads += 1;
        if (globalShowTimer) return;

        globalShowTimer = window.setTimeout(() => {
            globalShowTimer = null;
            if (activeGlobalLoads > 0) ensureGlobalLoader(text);
        }, SHOW_DELAY_MS);
    }

    function endGlobalLoad() {
        activeGlobalLoads = Math.max(0, activeGlobalLoads - 1);
        if (activeGlobalLoads > 0) return;

        if (globalShowTimer) {
            window.clearTimeout(globalShowTimer);
            globalShowTimer = null;
        }

        const elapsed = Date.now() - globalShownAt;
        const wait = Math.max(0, MIN_VISIBLE_MS - elapsed);
        window.setTimeout(() => {
            if (activeGlobalLoads === 0) publicApi.hideAuto();
        }, wait);
    }

    function shouldSkipLink(link, event) {
        if (
            !link ||
            link.dataset.noLoader !== undefined ||
            link.hasAttribute("download")
        )
            return true;
        if (
            event?.defaultPrevented ||
            event?.metaKey ||
            event?.ctrlKey ||
            event?.shiftKey ||
            event?.altKey
        )
            return true;
        if (link.target && link.target !== "_self") return true;
        const href = link.getAttribute("href") || "";
        if (
            !href ||
            href.startsWith("#") ||
            href.startsWith("javascript:") ||
            href.startsWith("mailto:") ||
            href.startsWith("tel:")
        )
            return true;

        try {
            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return true;
            if (
                url.pathname === window.location.pathname &&
                url.search === window.location.search &&
                url.hash
            )
                return true;
        } catch (_) {
            return true;
        }

        return false;
    }

    function shouldSkipRequest(url) {
        const value = String(url || "");
        return (
            value.includes("/build/assets/") ||
            value.includes("/css/") ||
            value.includes("/js/") ||
            value.includes("/favicon") ||
            value.includes("/bonbon-loader.")
        );
    }

    function installGlobalHooks() {
        if (window.__bonbonLoaderHooksInstalled) return;
        window.__bonbonLoaderHooksInstalled = true;

        document.addEventListener(
            "click",
            (event) => {
                const link = event.target?.closest?.("a[href]");
                if (shouldSkipLink(link, event)) return;
                beginGlobalLoad("Loading page...");
            },
            true,
        );

        document.addEventListener(
            "submit",
            (event) => {
                const form = event.target;
                if (
                    !form ||
                    form.dataset?.noLoader !== undefined ||
                    form.target === "_blank"
                )
                    return;
                beginGlobalLoad("Processing...");
            },
            true,
        );

        window.addEventListener("pageshow", () => {
            activeGlobalLoads = 0;
            publicApi.hideAuto();
        });

        if (window.fetch && !window.fetch.__bonbonLoaderWrapped) {
            const originalFetch = window.fetch.bind(window);
            const wrappedFetch = (...args) => {
                const url = args[0]?.url || args[0];
                const skip =
                    shouldSkipRequest(url) ||
                    args[1]?.headers?.["X-Bonbon-No-Loader"];
                if (!skip) beginGlobalLoad("Loading...");
                return originalFetch(...args).finally(() => {
                    if (!skip) endGlobalLoad();
                });
            };
            wrappedFetch.__bonbonLoaderWrapped = true;
            window.fetch = wrappedFetch;
        }

        if (
            window.XMLHttpRequest &&
            !window.XMLHttpRequest.prototype.__bonbonLoaderWrapped
        ) {
            const originalOpen = window.XMLHttpRequest.prototype.open;
            const originalSend = window.XMLHttpRequest.prototype.send;

            window.XMLHttpRequest.prototype.open = function (
                method,
                url,
                ...rest
            ) {
                this.__bonbonLoaderSkip = shouldSkipRequest(url);
                return originalOpen.call(this, method, url, ...rest);
            };

            window.XMLHttpRequest.prototype.send = function (...args) {
                if (!this.__bonbonLoaderSkip) {
                    beginGlobalLoad("Loading...");
                    this.addEventListener("loadend", endGlobalLoad, {
                        once: true,
                    });
                }
                return originalSend.apply(this, args);
            };

            window.XMLHttpRequest.prototype.__bonbonLoaderWrapped = true;
        }
    }

    const publicApi = {
        /**
         * Show loader inside a target element (inline).
         * @param {string|Element} target - CSS selector or DOM element
         */
        show(target, text = "") {
            const el = resolve(target);
            if (!el) return;
            this.hide(target); // remove existing
            const isSmall = el.offsetHeight < 200 || el.offsetWidth < 300;
            const overlay = createOverlay(text, false, isSmall);
            el.style.position = el.style.position || "relative";
            if (!el.style.position || el.style.position === "static")
                el.style.position = "relative";
            el.appendChild(overlay);
        },

        /**
         * Show fullscreen loader overlay.
         * @param {string} text - Optional loading text
         */
        showFullscreen(text = "") {
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
            const existing = el.querySelectorAll(":scope > ." + LOADER_CLASS);
            existing.forEach((o) => {
                o.classList.add("bb-loader-fadeout");
                setTimeout(() => o.remove(), 300);
            });
        },

        /**
         * Hide all loaders everywhere.
         */
        hideAll() {
            document.querySelectorAll("." + LOADER_CLASS).forEach((o) => {
                o.classList.add("bb-loader-fadeout");
                setTimeout(() => o.remove(), 300);
            });
        },

        hideAuto() {
            document
                .querySelectorAll(`.${LOADER_CLASS}[${AUTO_LOADER_ATTR}]`)
                .forEach((o) => {
                    o.classList.add("bb-loader-fadeout");
                    setTimeout(() => o.remove(), 300);
                });
        },

        start(text = "Loading...") {
            beginGlobalLoad(text);
        },

        done() {
            endGlobalLoad();
        },

        setTheme(theme) {
            const normalized = theme === "classic" ? "classic" : "baking";
            try {
                localStorage.setItem("bonbon-loader-theme", normalized);
            } catch (_) {}
            document.documentElement.dataset.loaderTheme = normalized;
        },

        getTheme,

        installGlobalHooks,
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", installGlobalHooks, {
            once: true,
        });
    } else {
        installGlobalHooks();
    }

    return publicApi;
})();
