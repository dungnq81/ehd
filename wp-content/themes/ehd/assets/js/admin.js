/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./wp-content/themes/ehd/resources/js/admin.js":
/*!*****************************************************!*\
  !*** ./wp-content/themes/ehd/resources/js/admin.js ***!
  \*****************************************************/
/***/ (function() {

eval("/*jshint esversion: 6 */\nvar $ = jQuery;\n'use strict';\n\n$(function () {\n  //...\n  var rank_math_dashboard_widget = $(\"#rank_math_dashboard_widget\");\n  rank_math_dashboard_widget.find('.rank-math-blog-title').remove();\n  rank_math_dashboard_widget.find('.rank-math-blog-post').remove(); //...\n  //const toplevel_page_mlang = $(\"#toplevel_page_mlang\");\n  //toplevel_page_mlang.find('a[href=\"admin.php?page=mlang_wizard\"]').closest('li').remove();\n  //...\n\n  var acf_textarea = $(\".acf-editor-wrap.html-active\").find('textarea.wp-editor-area');\n  acf_textarea.removeAttr(\"style\"); //...\n\n  var createuser = $(\"#createuser\");\n  createuser.find(\"#send_user_notification\").removeAttr(\"checked\").attr(\"disabled\", true); //...\n  //const site_review = $(\"#menu-posts-site-review\");\n  //site_review.find('a[href=\"edit.php?post_type=site-review&page=addons\"]').closest('li').remove();\n  //...\n  //const notice_error = $(\".notice.notice-error\");\n  //notice_error.find('a[href*=\"options-general.php?page=quickkbuy-setting\"]').closest('.notice.notice-error').remove();\n  //...\n  //const wpbody_content = $(\"#wpbody-content\");\n  //wpbody_content.find('a[href*=\"page=addons&post_type=site-review\"]').remove();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyIkIiwialF1ZXJ5IiwicmFua19tYXRoX2Rhc2hib2FyZF93aWRnZXQiLCJmaW5kIiwicmVtb3ZlIiwiYWNmX3RleHRhcmVhIiwicmVtb3ZlQXR0ciIsImNyZWF0ZXVzZXIiLCJhdHRyIl0sInNvdXJjZXMiOlsid2VicGFjazovLy8uL3dwLWNvbnRlbnQvdGhlbWVzL2VoZC9yZXNvdXJjZXMvanMvYWRtaW4uanM/ZGQ0OCJdLCJzb3VyY2VzQ29udGVudCI6WyIvKmpzaGludCBlc3ZlcnNpb246IDYgKi9cclxuY29uc3QgJCA9IGpRdWVyeTtcclxuXHJcbid1c2Ugc3RyaWN0JztcclxuJChmdW5jdGlvbiAoKSB7XHJcblxyXG4gICAgLy8uLi5cclxuICAgIGNvbnN0IHJhbmtfbWF0aF9kYXNoYm9hcmRfd2lkZ2V0ID0gJChcIiNyYW5rX21hdGhfZGFzaGJvYXJkX3dpZGdldFwiKTtcclxuICAgIHJhbmtfbWF0aF9kYXNoYm9hcmRfd2lkZ2V0LmZpbmQoJy5yYW5rLW1hdGgtYmxvZy10aXRsZScpLnJlbW92ZSgpO1xyXG4gICAgcmFua19tYXRoX2Rhc2hib2FyZF93aWRnZXQuZmluZCgnLnJhbmstbWF0aC1ibG9nLXBvc3QnKS5yZW1vdmUoKTtcclxuXHJcbiAgICAvLy4uLlxyXG4gICAgLy9jb25zdCB0b3BsZXZlbF9wYWdlX21sYW5nID0gJChcIiN0b3BsZXZlbF9wYWdlX21sYW5nXCIpO1xyXG4gICAgLy90b3BsZXZlbF9wYWdlX21sYW5nLmZpbmQoJ2FbaHJlZj1cImFkbWluLnBocD9wYWdlPW1sYW5nX3dpemFyZFwiXScpLmNsb3Nlc3QoJ2xpJykucmVtb3ZlKCk7XHJcblxyXG4gICAgLy8uLi5cclxuICAgIGNvbnN0IGFjZl90ZXh0YXJlYSA9ICQoXCIuYWNmLWVkaXRvci13cmFwLmh0bWwtYWN0aXZlXCIpLmZpbmQoJ3RleHRhcmVhLndwLWVkaXRvci1hcmVhJyk7XHJcbiAgICBhY2ZfdGV4dGFyZWEucmVtb3ZlQXR0cihcInN0eWxlXCIpO1xyXG5cclxuICAgIC8vLi4uXHJcbiAgICBjb25zdCBjcmVhdGV1c2VyID0gJChcIiNjcmVhdGV1c2VyXCIpO1xyXG4gICAgY3JlYXRldXNlci5maW5kKFwiI3NlbmRfdXNlcl9ub3RpZmljYXRpb25cIikucmVtb3ZlQXR0cihcImNoZWNrZWRcIikuYXR0cihcImRpc2FibGVkXCIsIHRydWUpO1xyXG5cclxuICAgIC8vLi4uXHJcbiAgICAvL2NvbnN0IHNpdGVfcmV2aWV3ID0gJChcIiNtZW51LXBvc3RzLXNpdGUtcmV2aWV3XCIpO1xyXG4gICAgLy9zaXRlX3Jldmlldy5maW5kKCdhW2hyZWY9XCJlZGl0LnBocD9wb3N0X3R5cGU9c2l0ZS1yZXZpZXcmcGFnZT1hZGRvbnNcIl0nKS5jbG9zZXN0KCdsaScpLnJlbW92ZSgpO1xyXG5cclxuICAgIC8vLi4uXHJcbiAgICAvL2NvbnN0IG5vdGljZV9lcnJvciA9ICQoXCIubm90aWNlLm5vdGljZS1lcnJvclwiKTtcclxuICAgIC8vbm90aWNlX2Vycm9yLmZpbmQoJ2FbaHJlZio9XCJvcHRpb25zLWdlbmVyYWwucGhwP3BhZ2U9cXVpY2trYnV5LXNldHRpbmdcIl0nKS5jbG9zZXN0KCcubm90aWNlLm5vdGljZS1lcnJvcicpLnJlbW92ZSgpO1xyXG5cclxuICAgIC8vLi4uXHJcbiAgICAvL2NvbnN0IHdwYm9keV9jb250ZW50ID0gJChcIiN3cGJvZHktY29udGVudFwiKTtcclxuICAgIC8vd3Bib2R5X2NvbnRlbnQuZmluZCgnYVtocmVmKj1cInBhZ2U9YWRkb25zJnBvc3RfdHlwZT1zaXRlLXJldmlld1wiXScpLnJlbW92ZSgpO1xyXG59KTsiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0EsSUFBTUEsQ0FBQyxHQUFHQyxNQUFWO0FBRUE7O0FBQ0FELENBQUMsQ0FBQyxZQUFZO0VBRVY7RUFDQSxJQUFNRSwwQkFBMEIsR0FBR0YsQ0FBQyxDQUFDLDZCQUFELENBQXBDO0VBQ0FFLDBCQUEwQixDQUFDQyxJQUEzQixDQUFnQyx1QkFBaEMsRUFBeURDLE1BQXpEO0VBQ0FGLDBCQUEwQixDQUFDQyxJQUEzQixDQUFnQyxzQkFBaEMsRUFBd0RDLE1BQXhELEdBTFUsQ0FPVjtFQUNBO0VBQ0E7RUFFQTs7RUFDQSxJQUFNQyxZQUFZLEdBQUdMLENBQUMsQ0FBQyw4QkFBRCxDQUFELENBQWtDRyxJQUFsQyxDQUF1Qyx5QkFBdkMsQ0FBckI7RUFDQUUsWUFBWSxDQUFDQyxVQUFiLENBQXdCLE9BQXhCLEVBYlUsQ0FlVjs7RUFDQSxJQUFNQyxVQUFVLEdBQUdQLENBQUMsQ0FBQyxhQUFELENBQXBCO0VBQ0FPLFVBQVUsQ0FBQ0osSUFBWCxDQUFnQix5QkFBaEIsRUFBMkNHLFVBQTNDLENBQXNELFNBQXRELEVBQWlFRSxJQUFqRSxDQUFzRSxVQUF0RSxFQUFrRixJQUFsRixFQWpCVSxDQW1CVjtFQUNBO0VBQ0E7RUFFQTtFQUNBO0VBQ0E7RUFFQTtFQUNBO0VBQ0E7QUFDSCxDQTlCQSxDQUFEIiwiZmlsZSI6Ii4vd3AtY29udGVudC90aGVtZXMvZWhkL3Jlc291cmNlcy9qcy9hZG1pbi5qcy5qcyIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./wp-content/themes/ehd/resources/js/admin.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./wp-content/themes/ehd/resources/js/admin.js"]();
/******/ 	
/******/ })()
;