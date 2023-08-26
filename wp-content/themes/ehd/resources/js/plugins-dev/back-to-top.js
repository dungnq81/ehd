(function () {
    'use strict';

    let smooth_scroll_back_to_top = false;

    // Feature Test
    if ('querySelector' in document && 'addEventListener' in window) {
        let goTopBtn = document.querySelector('.back-to-top');

        let trackScroll = function () {
            let scrolled = window.pageYOffset;
            let coords = goTopBtn.getAttribute('data-start-scroll');

            if (scrolled > coords) {
                goTopBtn.classList.add('back-to-top__show');
            }

            if (scrolled < coords) {
                goTopBtn.classList.remove('back-to-top__show');
            }
        };

        // Function to animate the scroll
        let smoothScroll = function (anchor, duration) {
            // Calculate how far and how fast to scroll
            let startLocation = window.pageYOffset;
            let endLocation = document.body.offsetTop;
            let distance = endLocation - startLocation;
            let increments = distance / (duration / 16);
            let stopAnimation;

            // Scroll the page by an increment, and check if it's time to stop
            let animateScroll = function () {
                window.scrollBy(0, increments);
                stopAnimation();
            };

            // Stop animation when you reach the anchor OR the top of the page
            stopAnimation = function () {
                let travelled = window.pageYOffset;
                if (travelled <= (endLocation || 0)) {
                    clearInterval(runAnimation);
                    document.activeElement.blur();
                }
            };

            // Loop the animation function
            let runAnimation = setInterval(animateScroll, 16);
        };

        if (goTopBtn) {
            // Show the button when scrolling down.
            window.addEventListener('scroll', trackScroll);

            // Scroll back to top when clicked.
            goTopBtn.addEventListener('click', function (e) {
                e.preventDefault();

                if (smooth_scroll_back_to_top) {
                    smoothScroll(document.body, goTopBtn.getAttribute('data-scroll-speed') || 400);
                } else {
                    window.scrollTo(0, 0);
                }
            }, false);
        }
    }
}());
