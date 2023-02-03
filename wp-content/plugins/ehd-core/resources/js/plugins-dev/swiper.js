import {nanoid} from 'nanoid';
import random from "lodash/random";
import isEmpty from "lodash/isEmpty";
import toString from "lodash/toString";

/** import Swiper bundle with all modules installed */
import {Swiper} from 'swiper/bundle';

/** swiper container */
const w_swiper = [...document.querySelectorAll('.w-swiper')];
w_swiper.forEach((el, index) => {

    const _rand = nanoid(12);
    const _class = 'swiper-' + _rand;
    const _next_class = 'next-' + _rand;
    const _prev_class = 'prev-' + _rand;

    el.classList.add(_class);

    /** swiper controls */
    let _controls = el.closest('.swiper-section').querySelector('.swiper-controls');
    if (_controls == null) {
        _controls = document.createElement("div");
        _controls.classList.add('swiper-controls');
        el.after(_controls);
    }

    /** swiper options */
    const el_swiper_wrapper = el.querySelector('.swiper-wrapper');
    let _obj_options = JSON.parse(el_swiper_wrapper.dataset.options);

    if (isEmpty(_obj_options)) {
        _obj_options = {
            autoview: !0,
            loop: !1,
            autoplay: !0,
            navigation: !0,
        };
    }

    /** init options */
    let _result_options = {
        grabCursor: !0,
        allowTouchMove: !0,
        threshold: 5,
        hashNavigation: !1,
        mousewheel: !1,
        wrapperClass: 'swiper-wrapper',
        slideClass: 'swiper-slide',
        slideActiveClass: 'swiper-slide-active'
    };

    /** responsive + gap + autoview */
    let _desktop_data = 1,
        _tablet_data = 1,
        _mobile_data = 1;

    if ("desktop" in _obj_options) {
        _desktop_data = _obj_options.desktop;
    }
    if ("tablet" in _obj_options) {
        _tablet_data = _obj_options.tablet;
    }
    if ("mobile" in _obj_options) {
        _mobile_data = _obj_options.mobile;
    }

    /** gap + autoview */
    let _desktop_gap = 0,
        _mobile_gap = 0;

    if ("desktop_gap" in _obj_options) {
        _desktop_gap = _obj_options.desktop_gap;
    }
    if ("mobile_gap" in _obj_options) {
        _mobile_gap = _obj_options.mobile_gap;
    }

    _result_options.spaceBetween = parseInt(_mobile_gap);
    if ("autoview" in _obj_options) {
        _result_options.slidesPerView = 'auto';
        _result_options.breakpoints = {
            640: {
                spaceBetween: parseInt(_desktop_gap),
            },
        };
    } else {
        _result_options.slidesPerView = parseInt(_mobile_data);
        _result_options.breakpoints = {
            640: {
                spaceBetween: parseInt(_desktop_gap),
                slidesPerView: parseInt(_tablet_data),
            },
            1024: {
                //spaceBetween: parseInt(_desktop_gap),
                slidesPerView: parseInt(_desktop_data),
            },
        };
    }

    /** centered */
    if ("centered" in _obj_options) {
        _result_options.centeredSlides = !0;
    }

    /** speed */
    if ("speed" in _obj_options) {
        _result_options.speed = parseInt(_obj_options.speed);
    } else {
        _result_options.speed = random(300, 900);
    }

    /** observer */
    if ("observer" in _obj_options) {
        _result_options.observer = !0;
        _result_options.observeParents = !0;
    }

    /** group */
    if ("group" in _obj_options && _result_options.slidesPerView > 1) {
        _result_options.slidesPerGroupSkip = 0;
        _result_options.slidesPerGroup = parseInt(_obj_options.group);
    }

    /** effect */
    if ("effect" in _obj_options) {
        _result_options.effect = toString(_obj_options.effect);
        if ('fade' === _result_options.effect) {
            _result_options.fadeEffect = { crossFade: !0 };
        }
    }

    /** autoheight */
    if ("autoheight" in _obj_options) {
        _result_options.autoHeight = !0;
    }

    /** freemode */
    if ("freemode" in _obj_options) {
        _result_options.freeMode = !0;
    }

    /** loop */
    if ("loop" in _obj_options && !("row" in _obj_options)) {
        _result_options.loop = !0;
    }

    /** autoplay */
    if ("autoplay" in _obj_options) {
        if ("delay" in _obj_options) {
            _result_options.autoplay = {
                disableOnInteraction: !1,
                delay: parseInt(_obj_options.delay),
            };
        } else {
            _result_options.autoplay = {
                disableOnInteraction: !1,
                delay: random(3000, 6000),
            };
        }
        if ("reverse" in _obj_options) {
            _result_options.reverseDirection = !0;
        }
    }

    /** rows */
    if ("row" in _obj_options) {
        _result_options.direction = 'horizontal';
        _result_options.loop = !1;
        _result_options.grid = {
            rows: parseInt(_obj_options.row),
            fill: 'row',
        };
    }

    /** direction */
    if ("direction" in _obj_options) {
        _result_options.direction = toString(_obj_options.direction);
    }

    /** navigation */
    if ("navigation" in _obj_options) {
        const _section = el.closest('.swiper-section');
        let _btn_prev = _section.querySelector('.swiper-button-prev');
        let _btn_next = _section.querySelector('.swiper-button-next');

        if (_btn_prev && _btn_next) {
            _btn_prev.classList.add(_prev_class);
            _btn_next.classList.add(_next_class);
        } else {
            _btn_prev = document.createElement("div");
            _btn_next = document.createElement("div");

            _btn_prev.classList.add('swiper-button', 'swiper-button-prev', _prev_class);
            _btn_next.classList.add('swiper-button', 'swiper-button-next', _next_class);

            _controls.appendChild(_btn_prev);
            _controls.appendChild(_btn_next);

            _btn_prev.setAttribute("data-glyph", "");
            _btn_next.setAttribute("data-glyph", "");
        }

        _result_options.navigation = {
            nextEl: '.' + _next_class,
            prevEl: '.' + _prev_class,
        };
    }

    /** pagination */
    if ("pagination" in _obj_options) {

    }

    /** parallax */
    if ("parallax" in _obj_options) {
        _result_options.parallax = !0;
    }

    /** marquee **/
    if ("marquee" in _obj_options) {
        _result_options.centeredSlides = !0;
        _result_options.autoplay = {
            delay: 1,
            disableOnInteraction: !1
        };
        _result_options.loop = !0;
        _result_options.allowTouchMove = !0;
    }

    /**cssMode*/
    if (!("row" in _obj_options)
        && !("marquee" in _obj_options)
        && !("centered" in _obj_options)
        && !("freemode" in _obj_options)
        && !("progressbar" in _obj_options)
        && ( is_mobile() || is_tablet() )
        && !el.classList.contains('sync-swiper')) {
        _result_options.cssMode = !0; /* API CSS Scroll Snap */
    }

    /** console.log(_obj_options); */
    let _swiper = new Swiper('.' + _class, _result_options);
    if (!("autoplay" in _obj_options) && !("marquee" in _obj_options)) {
        _swiper.autoplay.stop();
    }

    /** now add mouseover and mouseout events to pause and resume the autoplay; */
    el.addEventListener('mouseover', () => {
        _swiper.autoplay.stop();
    });

    el.addEventListener('mouseout', () => {
        if ("autoplay" in _obj_options) {
            _swiper.autoplay.start();
        }
    });
});
