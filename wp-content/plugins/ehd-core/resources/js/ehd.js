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

    // javascript disable right click
    //document.addEventListener('contextmenu', event => event.preventDefault());
    /*document.addEventListener("contextmenu", function(e){
        if (e.target.nodeName === "IMG") {
            e.preventDefault();
        }
    }, false);*/

    /**remove style img tag*/
    const _img = document.querySelectorAll('img');
    Array.prototype.forEach.call(_img, (el) => {
        el.removeAttribute('style');
    });
});

const $ = jQuery;

'use strict';
$(() => {

    /** Remove empty P tags created by WP inside of Accordion and Orbit */
    $('.accordion p:empty, .orbit p:empty').remove();
});
