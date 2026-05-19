import { initCakeScrollytelling } from './cake-scroll';

document.addEventListener('DOMContentLoaded', () => {
    try {
        initCakeScrollytelling();
    } catch (err) {
        console.error('CRITICAL INIT ERROR:', err);
        
        // Graceful fallback: dismiss the preloader so the user can still use the store shelf
        const preloader = document.getElementById('cake-preloader');
        if (preloader) {
            preloader.style.transition = 'opacity 0.8s ease, visibility 0.8s ease';
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            setTimeout(() => {
                try { preloader.remove(); } catch(e) {}
            }, 800);
        }
    }
});
