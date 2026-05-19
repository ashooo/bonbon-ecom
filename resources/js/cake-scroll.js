import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createCakeScene } from './cake-scene';

gsap.registerPlugin(ScrollTrigger);

export function initCakeScrollytelling() {
    const container = document.getElementById('cake-canvas-container');
    const scrollSection = document.getElementById('cake-scroll-section');

    if (!container || !scrollSection) return;

    // Navbar stays visible (sticky) during scrollytelling

    // Create 3D cake scene
    const cake = createCakeScene(container);
    const { state, assembledPositions } = cake;

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

    // Message 1 — fade in
    tl.fromTo('#story-msg-1', {
        opacity: 0,
        y: 50,
    }, {
        opacity: 1,
        y: 0,
        duration: 10,
        ease: 'power2.out',
    }, 3);

    // Message 1 — fade out
    tl.to('#story-msg-1', {
        opacity: 0,
        y: -30,
        duration: 7,
        ease: 'power2.in',
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
        duration: 22,
        ease: 'power2.out',
    }, 28);

    // Top layer settles down
    tl.to(state, {
        topY: assembledPositions.top.y + 0.3,
        duration: 18,
        ease: 'power2.inOut',
    }, 30);

    // Message 2
    tl.fromTo('#story-msg-2', {
        opacity: 0,
        y: 50,
    }, {
        opacity: 1,
        y: 0,
        duration: 10,
        ease: 'power2.out',
    }, 31);

    tl.to('#story-msg-2', {
        opacity: 0,
        y: -30,
        duration: 7,
        ease: 'power2.in',
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
        duration: 22,
        ease: 'power2.out',
    }, 54);

    // All layers settle to final positions
    tl.to(state, {
        topY: assembledPositions.top.y,
        middleY: assembledPositions.middle.y,
        duration: 18,
        ease: 'power2.inOut',
    }, 58);

    // Message 3
    tl.fromTo('#story-msg-3', {
        opacity: 0,
        y: 50,
    }, {
        opacity: 1,
        y: 0,
        duration: 10,
        ease: 'power2.out',
    }, 58);

    tl.to('#story-msg-3', {
        opacity: 0,
        y: -30,
        duration: 7,
        ease: 'power2.in',
    }, 74);

    // ═══════════════════════════════════════
    // Scene 4: Assembled + final CTA (80-100%)
    // ═══════════════════════════════════════
    tl.to(state, {
        cameraY: 2.6,
        cameraZ: 7.0,
        duration: 18,
        ease: 'power2.inOut',
    }, 78);

    // Final heading
    tl.fromTo('#story-msg-final', {
        opacity: 0,
        y: 50,
        scale: 0.96,
    }, {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 14,
        ease: 'power3.out',
    }, 82);

    // CTA button
    tl.fromTo('#story-cta-btn', {
        opacity: 0,
        y: 24,
    }, {
        opacity: 1,
        y: 0,
        duration: 10,
        ease: 'power2.out',
    }, 88);

    // ═══════════════════════════════════════
    // Cupcake + Signboard appear (90-100%)
    // ═══════════════════════════════════════

    // Show signboard (left side) — slides in from further left
    tl.to(state, {
        signboardVisible: true,
        duration: 0.1,
    }, 88);

    tl.fromTo(state, {
        signboardX: -5.5,
        signboardY: 3.5,
    }, {
        signboardX: -3.0,
        signboardY: 2.9,
        duration: 12,
        ease: 'back.out(1.2)',
    }, 88);

    // Signboard HTML overlay button
    tl.fromTo('#signboard-overlay', {
        opacity: 0,
        x: -40,
    }, {
        opacity: 1,
        x: 0,
        duration: 10,
        ease: 'power2.out',
    }, 92);

    // Show cupcake (right side) — slides in from further right
    tl.to(state, {
        cupcakeVisible: true,
        duration: 0.1,
    }, 89);

    tl.fromTo(state, {
        cupcakeX: 5.5,
        cupcakeY: 3.5,
    }, {
        cupcakeX: 3.0,
        cupcakeY: 2.9,
        duration: 12,
        ease: 'back.out(1.2)',
    }, 89);

    // Cupcake HTML overlay button
    tl.fromTo('#cupcake-overlay', {
        opacity: 0,
        x: 40,
    }, {
        opacity: 1,
        x: 0,
        duration: 10,
        ease: 'power2.out',
    }, 93);

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
