/* jshint esversion: 6 */
import './_foundation';

import {nanoid} from 'nanoid';
import random from "lodash/random";
import isEmpty from "lodash/isEmpty";
import toString from "lodash/toString";

/** current-device */
import device from "current-device";
const is_mobile = () => device.mobile();
const is_tablet = () => device.tablet();

/** Fancybox */
//import { Fancybox } from "@fancyapps/ui";

/** AOS */
//import AOS from 'aos';
//AOS.init();

const $ = jQuery;

/** Create deferred YT object */
// const YTdeferred = $.Deferred();
// window.onYouTubeIframeAPIReady = function () {
//     YTdeferred.resolve(window.YT);
// };

//require("jquery.marquee");

$(() => {
    //...
});

/** vars */
const getParameters = (URL) => JSON.parse('{"' + decodeURI(URL.split("?")[1]).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
const touchSupported = () => { ('ontouchstart' in window || window.DocumentTouch && document instanceof window.DocumentTouch); };

/**
 * https://stackoverflow.com/questions/1248081/how-to-get-the-browser-viewport-dimensions
 *
 * @param w
 * @returns {{w: *, h: *}}
 */
function getViewportSize(w) {

    /* Use the specified window or the current window if no argument*/
    w = w || window;

    /* This works for all browsers except IE8 and before*/
    if (w.innerWidth != null) return {w: w.innerWidth, h: w.innerHeight};

    /* For IE (or any browser) in Standards mode*/
    let d = w.document;
    if ("CSS1Compat" === document.compatMode)
        return {
            w: d.documentElement.clientWidth,
            h: d.documentElement.clientHeight
        };

    /* For browsers in Quirks mode*/
    return {w: d.body.clientWidth, h: d.body.clientHeight};
}

/**
 * @param url
 * @param $delay
 */
function redirect(url = null, $delay = 10) {
    setTimeout(function () {
        if (url === null || url === '' || typeof url === "undefined") {
            document.location.assign(window.location.href);
        } else {
            url = url.replace(/\s+/g, '');
            document.location.assign(url);
        }
    }, $delay);
}

/**
 * @param page
 * @param title
 * @param url
 */
function pushState(page, title, url) {
    if ("undefined" !== typeof history.pushState) {
        history.pushState({page: page}, title, url);
    } else {
        window.location.assign(url);
    }
}
