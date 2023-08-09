/*jshint esversion: 6 */
const $ = jQuery;

import { nanoid } from 'nanoid';
import Cookies from 'js-cookie';

'use strict';
$(function () {

    /**
     * @param el
     * @return {*|jQuery}
     */
    function rand_element_init(el) {
        const _rand = nanoid(9);
        $(el).addClass(_rand);

        let _id = $(el).attr('id');
        if (_id === 'undefined' || _id === '') {
            _id = _rand;
            $(el).attr('id', _id);
        }

        return _id;
    }

    // codemirror
    const codemirror_css = $(".codemirror_css");
    const codemirror_html = $(".codemirror_html");

    codemirror_css.each((index, el) => {
        rand_element_init(el);

        let editorSettings = codemirror_settings.codemirror_css ? _.clone(codemirror_settings.codemirror_css) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                indentUnit: 3,
                tabSize: 3,
                //lineNumbers: true,
                autoRefresh: true,
            }
        );

        wp.codeEditor.initialize($(el), editorSettings);
    });

    codemirror_html.each((index, el) => {
        rand_element_init(el);

        let editorSettings = codemirror_settings.codemirror_html ? _.clone(codemirror_settings.codemirror_html) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                indentUnit: 3,
                tabSize: 3,
                autoRefresh: true,
            }
        );

        wp.codeEditor.initialize($(el), editorSettings);
    });

    // notice
    const notice_dismiss = $(".notice-dismiss");
    notice_dismiss.on('click', function () {
       $(this).closest('.notice.is-dismissible').fadeOut();
    });

    // filter tabs
    const tabs_wrapper = $(".filter-tabs");
    tabs_wrapper.each((index, el) => {
        let _id = rand_element_init(el);

        const _nav = $(el).find(".tabs-nav");
        const _content = $(el).find(".tabs-content");

        _content.find('.tabs-panel').hide();
        let _cookie = 'cookie_' + _id + '_' + index;

        if (Cookies.get(_cookie) === '' || Cookies.get(_cookie) === 'undefined') {
            let _hash = _nav.find('a:first').attr("href");
            Cookies.set(_cookie, _hash, { expires: 7, path: '' });
        }

        _nav.find('a[href="' + Cookies.get(_cookie) + '"]').addClass("current");
        _nav.find('a').on("click", function (e) {
            e.preventDefault();
            Cookies.set(_cookie, $(this).attr("href"), { expires: 7, path: '' });

            _nav.find('a.current').removeClass("current");
            _content.find('.tabs-panel:visible').removeClass('show').hide();
            $(this.hash).addClass("show").show();
            $(this).addClass("current");

        }).filter(".current").trigger('click');
    });

    // user
    const create_user = $("#createuser");
    create_user.find("#send_user_notification").removeAttr("checked").attr("disabled", true);

    //...
    $("input[value=\"advanced-custom-fields-pro/acf.php\"]").remove();
    $("input[value=\"ehd-core/ehd-core.php\"]").remove();
});
