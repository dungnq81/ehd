/*jshint esversion: 6 */
const $ = jQuery;

import {nanoid} from 'nanoid';

'use strict';
$(function () {

    //...
    const notice_dismiss = $(".notice-dismiss");
    notice_dismiss.on('click', function () {
       $(this).closest('.notice.is-dismissible').fadeOut();
    });

    // tabs
    const tabs_wrapper = $(".filter-tabs");
    tabs_wrapper.each((index, el) => {
        const _rand = nanoid(9);
        $(el).addClass(_rand);

        let _id = $(el).attr('id');
        if (_id === undefined || _id === '') {
            _id = _rand;
            $(el).attr('id', _id);
        }

        const _nav = $(el).find(".tabs-nav");
        const _content = $(el).find(".tabs-content");

        _content.find('.tabs-panel').hide();
        let _cookie = 'cookie_' + _id + '_' + index;

        if (_getCookie(_cookie) === '' || _getCookie(_cookie) === 'undefined') {
            let _hash = _nav.find('a:first').attr("href");
            _setCookie(_cookie, _hash, 100);
        }

        _nav.find('a[href="' + _getCookie(_cookie) + '"]').addClass("current");
        _nav.find('a').on("click", function (e) {
            e.preventDefault();

            let _hash = $(this).attr("href");
            _setCookie(_cookie, _hash, 100);

            _nav.find('a.current').removeClass("current");
            _content.find('.tabs-panel:visible').removeClass('show').hide();
            $(this.hash).addClass("show").fadeIn();
            $(this).addClass("current");

        }).filter(".current").trigger('click');
    });

    //...
    const createuser = $("#createuser");
    createuser.find("#send_user_notification").removeAttr("checked").attr("disabled", true);

    //...
    $("input[value=\"advanced-custom-fields-pro/acf.php\"]").remove();
    $("input[value=\"ehd-core/ehd-core.php\"]").remove();
});

/**
 *
 * @param cname
 * @param cvalue
 * @param exdays
 */
function _setCookie(cname, cvalue, exdays) {
    let d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

/**
 *
 * @param cname
 * @returns {string}
 */
function _getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
