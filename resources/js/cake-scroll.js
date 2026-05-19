import * as THREE from 'three';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createCakeScene } from './cake-scene';

gsap.registerPlugin(ScrollTrigger);

export function initCakeScrollytelling() {
    const container = document.getElementById('cake-canvas-container');
    const scrollSection = document.getElementById('cake-scroll-section');

    if (!container || !scrollSection) return;

    // Create 3D cake scene
    const cake = createCakeScene(container);
    const { state, camera, assembledPositions, mainCakeGroup, createGalleryCake, galleryCakes } = cake;

    // --- GSAP ScrollTrigger Timeline ---
    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: scrollSection,
            start: 'top top',
            end: 'bottom bottom',
            scrub: 1.5,
            pin: '#cake-sticky-panel',
            anticipatePin: 1,
        },
    });

    // ═══════════════════════════════════════
    // Scene 1: Top layer floats down (0-25%)
    // ═══════════════════════════════════════
    tl.fromTo(state, {
        topY: 7,
        topOpacity: 0,
    }, {
        topY: assembledPositions.top.y + 1.0,
        topOpacity: 1,
        duration: 22,
        ease: 'power2.out',
    }, 0);

    tl.fromTo('#story-msg-1', {
        opacity: 0, y: 50,
    }, {
        opacity: 1, y: 0,
        duration: 10, ease: 'power2.out',
    }, 3);

    tl.to('#story-msg-1', {
        opacity: 0, y: -30,
        duration: 7, ease: 'power2.in',
    }, 18);

    // ═══════════════════════════════════════
    // Scene 2: Middle layer rises (28-52%)
    // ═══════════════════════════════════════
    tl.fromTo(state, {
        middleY: -5,
        middleOpacity: 0,
    }, {
        middleY: assembledPositions.middle.y,
        middleOpacity: 1,
        duration: 22, ease: 'power2.out',
    }, 28);

    tl.to(state, {
        topY: assembledPositions.top.y + 0.3,
        duration: 18, ease: 'power2.inOut',
    }, 30);

    tl.fromTo('#story-msg-2', {
        opacity: 0, y: 50,
    }, {
        opacity: 1, y: 0,
        duration: 10, ease: 'power2.out',
    }, 31);

    tl.to('#story-msg-2', {
        opacity: 0, y: -30,
        duration: 7, ease: 'power2.in',
    }, 46);

    // ═══════════════════════════════════════
    // Scene 3: Bottom layer rises (54-78%)
    // ═══════════════════════════════════════
    tl.fromTo(state, {
        bottomY: -5,
        bottomOpacity: 0,
    }, {
        bottomY: assembledPositions.bottom.y,
        bottomOpacity: 1,
        duration: 22, ease: 'power2.out',
    }, 54);

    tl.to(state, {
        topY: assembledPositions.top.y,
        middleY: assembledPositions.middle.y,
        duration: 18, ease: 'power2.inOut',
    }, 58);

    tl.fromTo('#story-msg-3', {
        opacity: 0, y: 50,
    }, {
        opacity: 1, y: 0,
        duration: 10, ease: 'power2.out',
    }, 58);

    tl.to('#story-msg-3', {
        opacity: 0, y: -30,
        duration: 7, ease: 'power2.in',
    }, 74);

    // ═══════════════════════════════════════
    // Scene 4: Assembled + final CTA (80-100%)
    // ═══════════════════════════════════════
    tl.to(state, {
        cameraY: 2.6,
        cameraZ: 7.0,
        duration: 18, ease: 'power2.inOut',
    }, 78);

    tl.fromTo('#story-msg-final', {
        opacity: 0, y: 50, scale: 0.96,
    }, {
        opacity: 1, y: 0, scale: 1,
        duration: 14, ease: 'power3.out',
    }, 82);

    tl.fromTo('#story-cta-btn', {
        opacity: 0, y: 24,
    }, {
        opacity: 1, y: 0,
        duration: 10, ease: 'power2.out',
    }, 88);

    // ═══════════════════════════════════════
    // Cupcake + Signboard appear (90-100%)
    // ═══════════════════════════════════════
    tl.to(state, { signboardVisible: true, duration: 0.1 }, 88);
    tl.fromTo(state, {
        signboardX: -5.5, signboardY: 3.5,
    }, {
        signboardX: -3.0, signboardY: 2.9,
        duration: 12, ease: 'back.out(1.2)',
    }, 88);

    tl.fromTo('#signboard-overlay', {
        opacity: 0, x: -40,
    }, {
        opacity: 1, x: 0,
        duration: 10, ease: 'power2.out',
    }, 92);

    tl.to(state, { cupcakeVisible: true, duration: 0.1 }, 89);
    tl.fromTo(state, {
        cupcakeX: 5.5, cupcakeY: 3.5,
    }, {
        cupcakeX: 3.0, cupcakeY: 2.9,
        duration: 12, ease: 'back.out(1.2)',
    }, 89);

    tl.fromTo('#cupcake-overlay', {
        opacity: 0, x: 40,
    }, {
        opacity: 1, x: 0,
        duration: 10, ease: 'power2.out',
    }, 93);


    // ═══════════════════════════════════════════════════════
    // "Explore Our Cakes" → Cinematic Gallery Transition
    // ═══════════════════════════════════════════════════════
    let galleryActive = false;

    // Layout config — determines 3D positions for each slot
    const MAX_PER_ROW = 4;
    const CAKE_SPACING = 3.8; // world-units between cakes (horizontal)
    const CAKE_SPACING_Y = 6.5; // world-units between cakes (vertical)
    const HERO_SCALE = 0.58;  // scale for hero cake after zoom-out
    const GALLERY_SCALE = 0.55; // scale for product cakes

    let isMobileGallery = false;
    let labelUpdateFrameId = null;
    const activeLabels = [];

    // --- Raycaster Variables ---
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2(-2, -2);
    let hoveredCakeGroup = null;
    let hoveredProduct = null;

    // Slot 0 is always the hero; products fill slots 1, 2, 3 ...
    function getSlotX(slotIndex, totalCakes) {
        if (isMobileGallery) return 0;
        
        // Base startX on the first row's width so all columns align perfectly in a grid
        const firstRowCakes = Math.min(MAX_PER_ROW, totalCakes);
        const totalWidth = (firstRowCakes - 1) * CAKE_SPACING;
        const startX = -(totalWidth / 2);
        
        const col = slotIndex % MAX_PER_ROW;
        return startX + col * CAKE_SPACING;
    }

    function getSlotY(slotIndex) {
        if (isMobileGallery) {
            return 1.5 - (slotIndex * CAKE_SPACING_Y);
        } else {
            const row = Math.floor(slotIndex / MAX_PER_ROW);
            return 0 - (row * CAKE_SPACING_Y); // Desktop stacks rows downwards from 0
        }
    }

    function activateGallery(e) {
        if (e) e.preventDefault();
        if (galleryActive) return;
        galleryActive = true;

        isMobileGallery = window.innerWidth <= 768;

        // Lock page scrolling
        document.body.style.overflow = 'hidden';

        const allProducts = window.__cakeProducts || [];
        
        // Reset filters
        const searchInput = document.getElementById('gallery-search');
        const categorySelect = document.getElementById('gallery-category');
        const sortSelect = document.getElementById('gallery-sort');
        if (searchInput) searchInput.value = '';
        if (categorySelect) categorySelect.value = '';
        if (sortSelect) sortSelect.value = 'name_asc';
        
        // Bind listeners if not bound
        if (searchInput && !searchInput._hasListener) {
            searchInput.addEventListener('input', applyFilters);
            categorySelect.addEventListener('change', applyFilters);
            sortSelect.addEventListener('change', applyFilters);
            searchInput._hasListener = true;
        }

        currentFilteredProducts = [...allProducts].sort((a, b) => a.name.localeCompare(b.name));
        const totalSlots = currentFilteredProducts.length;

        // --- Phase 1: Fade out UI text & hide scene extras ---
        gsap.to('#story-msg-final', { opacity: 0, duration: 0.4 });
        gsap.to('#story-cta-btn', { opacity: 0, duration: 0.3 });
        gsap.to('#signboard-overlay, #cupcake-overlay', { opacity: 0, duration: 0.25 });
        gsap.to(state, { cupcakeVisible: false, signboardVisible: false, duration: 0.01 });

        const galleryTl = gsap.timeline();

        // 2a. Scale the original hero cake down to 0 to hide it
        galleryTl.to(mainCakeGroup.scale, {
            x: 0, y: 0, z: 0,
            duration: 1.0, ease: 'power3.inOut',
        }, 0);

        let topCameraY, topLookY;

        if (!isMobileGallery) {
            // DESKTOP: Grid Layout
            topCameraY = 2.2;
            topLookY = 1.2;

            galleryTl.to(state, {
                cameraZ: 13,
                cameraY: topCameraY,
                cameraLookX: 0, // Center on the whole grid
                cameraLookY: topLookY,
                duration: 1.4, ease: 'power2.inOut',
            }, 0);
        } else {
            // MOBILE: Vertical Layout
            topCameraY = 2.8;
            topLookY = getSlotY(0) + 0.5;

            // Keep camera zoomed in for mobile so cakes are clearly visible
            galleryTl.to(state, {
                cameraZ: 7.5,
                cameraY: topCameraY,
                cameraLookX: 0,
                cameraLookY: topLookY,
                duration: 1.4, ease: 'power2.inOut',
            }, 0);
        }

        // --- Phase 3: Display Filter Bar & Render Initial Gallery ---
        gsap.to('#gallery-filter-bar', {
            display: 'flex',
            opacity: 1,
            y: 0,
            duration: 0.6,
            delay: 1.0,
            ease: 'power2.out'
        });

        // The renderFilteredGallery handles spawning cakes and setting up scroll
        setTimeout(() => {
            renderFilteredGallery(true);
        }, 800);

        // Show the "Go Back" button
        gsap.to('#gallery-back-btn', {
            display: 'flex',
            opacity: 1,
            y: 0,
            duration: 0.6,
            delay: 1.5,
            ease: 'power2.out'
        });
    }

    function deactivateGallery(e) {
        if (e) e.preventDefault();
        if (!galleryActive) return;
        galleryActive = false;

        // Unlock page scrolling
        document.body.style.overflow = '';

        // Clean up scroll listener and hide overlay
        const scrollOverlay = document.getElementById('gallery-scroll-overlay');
        if (scrollOverlay) {
            if (scrollOverlay._handleScroll) {
                scrollOverlay.removeEventListener('scroll', scrollOverlay._handleScroll);
                delete scrollOverlay._handleScroll;
            }
            scrollOverlay.style.display = 'none';
        }

        // Hide "Go Back" button and Filter Bar
        gsap.to('#gallery-back-btn, #gallery-filter-bar', {
            opacity: 0,
            y: 20,
            duration: 0.4,
            onComplete: () => {
                const btn = document.getElementById('gallery-back-btn');
                const filterBar = document.getElementById('gallery-filter-bar');
                if (btn) btn.style.display = 'none';
                if (filterBar) filterBar.style.display = 'none';
            }
        });

        // Hide and remove all gallery labels
        const labels = document.querySelectorAll('.gallery-label');
        labels.forEach(l => {
            gsap.to(l, { opacity: 0, duration: 0.3, onComplete: () => l.remove() });
        });
        activeLabels.length = 0; // Clear tracked labels

        const galleryTl = gsap.timeline();

        // 1. Animate gallery cakes away
        galleryCakes.forEach(cake => {
            gsap.to(cake.scale, { x: 0, y: 0, z: 0, duration: 0.6, ease: 'power2.in' });
            gsap.to(cake.position, { x: '+=15', duration: 0.6, ease: 'power2.in' });
        });

        // 2. Restore hero cake scale and position
        galleryTl.to(mainCakeGroup.scale, {
            x: 1, y: 1, z: 1,
            duration: 1.2, ease: 'power3.inOut'
        }, 0.2);

        // We also need to restore mainCakeGroup's Y position in case it was moved (mobile or desktop grid)
        galleryTl.to(mainCakeGroup.position, {
            y: 0,
            duration: 1.2, ease: 'power3.inOut'
        }, 0.2);

        galleryTl.to(state, {
            mainCakeX: 0,
            cameraZ: 7.0, // Scene 4 camera config
            cameraY: 2.6,
            cameraLookX: 0,
            cameraLookY: 1.4,
            duration: 1.2,
            ease: 'power3.inOut'
        }, 0.2);

        // 3. Restore UI elements and decorations
        galleryTl.call(() => {
            gsap.to('#story-msg-final, #story-cta-btn', { opacity: 1, duration: 0.6 });
            gsap.to('#signboard-overlay, #cupcake-overlay', { opacity: 1, duration: 0.6 });
            state.cupcakeVisible = true;
            state.signboardVisible = true;
        }, null, null, 1.2);

        // 4. Cleanup gallery cakes completely after animation finishes
        setTimeout(() => {
            galleryCakes.forEach(c => {
                if (c.parent) c.parent.remove(c);
            });
            galleryCakes.length = 0; // clear the tracking array
        }, 1500);
    }

    let currentFilteredProducts = [];

    function applyFilters() {
        const searchInput = document.getElementById('gallery-search');
        const categorySelect = document.getElementById('gallery-category');
        const sortSelect = document.getElementById('gallery-sort');

        if (!searchInput || !categorySelect || !sortSelect) return;

        const searchText = searchInput.value.toLowerCase().trim();
        const categoryId = categorySelect.value;
        const sortMode = sortSelect.value;

        const allProducts = window.__cakeProducts || [];
        
        let filtered = allProducts.filter(p => {
            if (searchText && !p.name.toLowerCase().includes(searchText) && !(p.description && p.description.toLowerCase().includes(searchText))) {
                return false;
            }
            if (categoryId && String(p.category_id) !== String(categoryId)) {
                return false;
            }
            return true;
        });

        filtered.sort((a, b) => {
            const priceA = Number(a.effective_price || a.price);
            const priceB = Number(b.effective_price || b.price);
            switch (sortMode) {
                case 'price_low': return priceA - priceB;
                case 'price_high': return priceB - priceA;
                case 'name_desc': return b.name.localeCompare(a.name);
                case 'name_asc': 
                default:
                    return a.name.localeCompare(b.name);
            }
        });

        currentFilteredProducts = filtered;
        renderFilteredGallery(false);
    }

    function renderFilteredGallery(isInitial = false) {
        const totalSlots = currentFilteredProducts.length;

        // 1. Animate out old cakes if re-filtering
        if (!isInitial && galleryCakes.length > 0) {
            galleryCakes.forEach(cake => {
                gsap.to(cake.scale, { x: 0, y: 0, z: 0, duration: 0.4, ease: 'power2.in', onComplete: () => {
                    if (cake.parent) cake.parent.remove(cake);
                }});
            });
            // Keep the array intact for GSAP, but clear it for new spawns
            galleryCakes.length = 0;
            
            // Remove old labels
            const labels = document.querySelectorAll('.gallery-label');
            labels.forEach(l => {
                gsap.to(l, { opacity: 0, duration: 0.3, onComplete: () => l.remove() });
            });
            activeLabels.length = 0;
        }

        // 2. Recalculate Scroll Bounds
        const maxRow = isMobileGallery ? Math.max(0, totalSlots - 1) : Math.max(0, Math.floor((totalSlots - 1) / MAX_PER_ROW));
        const totalSpanY = maxRow * CAKE_SPACING_Y;

        let topCameraY = isMobileGallery ? 2.8 : 2.2;
        let topLookY = isMobileGallery ? getSlotY(0) + 0.5 : 1.2;

        const scrollOverlay = document.getElementById('gallery-scroll-overlay');
        const scrollContent = document.getElementById('gallery-scroll-overlay-content');
        
        if (scrollOverlay && scrollContent) {
            const rowCount = maxRow + 1;
            scrollContent.style.height = `${rowCount * 100}vh`;
            scrollOverlay.scrollTop = 0;

            if (scrollOverlay._handleScroll) {
                scrollOverlay.removeEventListener('scroll', scrollOverlay._handleScroll);
            }

            if (totalSpanY > 0) {
                scrollOverlay.style.display = 'block';
                scrollOverlay._handleScroll = () => {
                    const maxScroll = scrollOverlay.scrollHeight - scrollOverlay.clientHeight;
                    if (maxScroll <= 0) return;
                    const progress = scrollOverlay.scrollTop / maxScroll;
                    state.cameraY = topCameraY - (progress * totalSpanY);
                    state.cameraLookY = topLookY - (progress * totalSpanY);
                };
                scrollOverlay.addEventListener('scroll', scrollOverlay._handleScroll);
            } else {
                scrollOverlay.style.display = 'none';
                state.cameraY = topCameraY;
                state.cameraLookY = topLookY;
            }
        }

        // 3. Spawn new cakes
        const spawnTl = gsap.timeline();
        currentFilteredProducts.forEach((product, i) => {
            const slotIndex = i;
            const finalX = isMobileGallery ? 0 : getSlotX(slotIndex, totalSlots);
            const finalY = isMobileGallery ? getSlotY(slotIndex) : getSlotY(slotIndex);
            
            // If it's the initial load, stagger them slowly. If refiltering, spawn faster.
            const delay = isInitial ? (0.2 + i * 0.55) : (0.2 + i * 0.15); 

            spawnTl.call(() => {
                const startX = isMobileGallery ? 0 : finalX + 18;
                const startY = isMobileGallery ? finalY - 10 : finalY;
                const galleryCake = createGalleryCake(product.name, startX, startY, GALLERY_SCALE);
                galleryCake.userData.product = product; // Add for raycaster detection
                galleryCake.visible = true;

                if (!isMobileGallery) {
                    gsap.to(galleryCake.position, { x: finalX, duration: isInitial ? 1.0 : 0.8, ease: 'power3.out' });
                    gsap.fromTo(galleryCake.position, { y: finalY - 1.2 }, { y: finalY, duration: isInitial ? 1.0 : 0.8, ease: 'power2.out' });
                } else {
                    gsap.to(galleryCake.position, { y: finalY, duration: isInitial ? 1.2 : 0.8, ease: 'power3.out' });
                }

                gsap.delayedCall(isInitial ? 0.7 : 0.4, () => {
                    showGalleryLabel(product, galleryCake, slotIndex, totalSlots);
                });
            }, null, null, delay);
        });
    }

    function updateLabels() {
        if (!galleryActive) {
            labelUpdateFrameId = null;
            return;
        }

        const width = window.innerWidth;
        const height = window.innerHeight;

        // RAYCASTING FOR CLICK EFFECTS
        if (camera && galleryCakes.length > 0) {
            raycaster.setFromCamera(mouse, camera);
            
            // Raycast only against the meshes inside the galleryCakes
            const intersects = raycaster.intersectObjects(galleryCakes, true);

            if (intersects.length > 0) {
                // Find parent group
                let hitObject = intersects[0].object;
                let hitGroup = null;
                while (hitObject) {
                    if (galleryCakes.includes(hitObject)) {
                        hitGroup = hitObject;
                        break;
                    }
                    hitObject = hitObject.parent;
                }

                if (hitGroup && hitGroup !== hoveredCakeGroup) {
                    hoveredCakeGroup = hitGroup;
                    hoveredProduct = hitGroup.userData.product;
                    document.body.style.cursor = 'pointer';
                }
            } else {
                if (hoveredCakeGroup) {
                    hoveredCakeGroup = null;
                    hoveredProduct = null;
                    document.body.style.cursor = 'default';
                }
            }
        }

        activeLabels.forEach(item => {
            if (!item.cake || !item.el) return;

            // Calculate vertical distance from camera
            const distance = camera.position.y - item.cake.position.y;
            
            // Hide labels if the cake is too far away (approx 2 rows)
            if (distance > 15 || distance < -5) {
                item.el.style.visibility = 'hidden';
                return; // Skip projecting if hidden
            } else {
                item.el.style.visibility = 'visible';
            }

            // Clone 3D position and project to 2D
            const pos = item.cake.position.clone();
            
            // Adjust label Y offset in 3D space depending on mobile/desktop
            // Move labels ABOVE the cakes as requested
            if (isMobileGallery) {
                pos.y += 2.0; 
            } else {
                pos.y += 2.2;
            }

            pos.project(camera);

            const x = (pos.x * 0.5 + 0.5) * width;
            const y = (pos.y * -0.5 + 0.5) * height;

            item.el.style.left = `${x}px`;
            item.el.style.top = `${y}px`;
        });

        labelUpdateFrameId = requestAnimationFrame(updateLabels);
    }

    // Creates a floating HTML name/price tag positioned in screen space tracking 3D cake
    function showGalleryLabel(product, cakeGroup, slotIndex, totalSlots) {
        const canvasContainer = document.getElementById('cake-canvas-container');
        if (!canvasContainer) return;

        const label = document.createElement('div');
        label.className = 'gallery-label';
        label.innerHTML = `
            <span class="gallery-label-name">${product.name}</span>
            <span class="gallery-label-price">₱${Number(product.price).toLocaleString()}</span>
        `;
        label.style.opacity = '0';
        label.style.transform = 'translate(-50%, -50%)'; // center origin on projected 3D point
        canvasContainer.appendChild(label);

        activeLabels.push({ el: label, cake: cakeGroup });

        if (!labelUpdateFrameId) {
            updateLabels();
        }

        gsap.to(label, {
            opacity: 1,
            duration: 0.5,
            ease: 'power2.out',
        });
    }

    // Attach gallery trigger
    const ctaBtn = document.getElementById('story-cta-btn');
    if (ctaBtn) {
        ctaBtn.addEventListener('click', activateGallery);
    }

    const backBtn = document.getElementById('gallery-back-btn');
    if (backBtn) {
        backBtn.addEventListener('click', deactivateGallery);
    }

    // Modal logic
    function openProductModal(product) {
        const modal = document.getElementById('gallery-product-modal');
        if (!modal) return;

        document.getElementById('gallery-modal-title').textContent = product.name;
        document.getElementById('gallery-modal-price').textContent = `₱${Number(product.effective_price || product.price).toLocaleString()}`;
        document.getElementById('gallery-modal-desc').textContent = product.description || '';
        document.getElementById('gallery-modal-product-id').value = product.id;
        
        const stockEl = document.getElementById('gallery-modal-stock');
        stockEl.textContent = product.stock_status === 'in_stock' ? 'In Stock' : (product.stock_status === 'made_to_order' ? 'Made to Order' : 'Out of Stock');
        
        gsap.to(modal, {
            display: 'flex',
            opacity: 1,
            duration: 0.4,
            ease: 'power2.out'
        });
    }

    function closeProductModal() {
        const modal = document.getElementById('gallery-product-modal');
        if (!modal) return;

        gsap.to(modal, {
            opacity: 0,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: () => {
                modal.style.display = 'none';
            }
        });
    }

    document.getElementById('gallery-modal-close')?.addEventListener('click', closeProductModal);
    document.getElementById('gallery-modal-backdrop')?.addEventListener('click', closeProductModal);

    // Track mouse for raycasting
    window.addEventListener('mousemove', (e) => {
        if (!galleryActive) return;
        mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
    });

    // Detect click for raycasting
    window.addEventListener('click', (e) => {
        if (!galleryActive || !hoveredProduct) return;
        
        // Prevent opening if clicking on other UI
        if (e.target.closest('#gallery-filter-bar') || e.target.closest('#gallery-back-btn') || e.target.closest('.gallery-product-modal')) {
            return;
        }

        openProductModal(hoveredProduct);
    });

    // --- Preloader dismiss ---
    const preloader = document.getElementById('cake-preloader');
    if (preloader) {
        gsap.to(preloader, {
            opacity: 0,
            duration: 0.8,
            delay: 1.8,
            ease: 'power2.inOut',
            onComplete: () => {
                preloader.style.visibility = 'hidden';
                preloader.style.pointerEvents = 'none';
            },
        });
    }
}
