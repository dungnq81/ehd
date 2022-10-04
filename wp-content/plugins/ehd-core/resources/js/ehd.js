import {nanoid} from 'nanoid';

/** DOMContentLoaded */
document.addEventListener('DOMContentLoaded', () => {

    /*attribute target="_blank" is not W3C compliant*/
    const _blanks = [...document.querySelectorAll('a._blank, a.blank, a[target="_blank"]')];
    _blanks.forEach((el, index) => {
        el.removeAttribute('target');
        el.setAttribute('target', '_blank');
        if (!1 === el.hasAttribute('rel')) {
            el.setAttribute('rel', 'noopener noreferrer nofollow');
        }
    });
});

/** import Swiper bundle with all modules installed */
import {Swiper} from 'swiper/bundle';

/** wc product gallery */
const spg_swiper = [...document.querySelectorAll('.swiper-product-gallery')];

/** swiper container */
const w_swiper = [...document.querySelectorAll('.w-swiper')];