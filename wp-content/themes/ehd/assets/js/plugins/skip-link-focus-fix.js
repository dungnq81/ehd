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

/***/ "./wp-content/themes/ehd/resources/js/plugins-dev/skip-link-focus-fix.js":
/*!*******************************************************************************!*\
  !*** ./wp-content/themes/ehd/resources/js/plugins-dev/skip-link-focus-fix.js ***!
  \*******************************************************************************/
/***/ (function() {

eval("/**\r\n * File skip-link-focus-fix.js\r\n *\r\n * Helps with accessibility for keyboard only users.\r\n * This is the source file for what is minified in the astra_skip_link_focus_fix() PHP function.\r\n *\r\n * Learn more: https://github.com/Automattic/_s/pull/136\r\n *\r\n * @package Astra\r\n */\n(function () {\n  var is_webkit = navigator.userAgent.toLowerCase().indexOf('webkit') > -1,\n      is_opera = navigator.userAgent.toLowerCase().indexOf('opera') > -1,\n      is_ie = navigator.userAgent.toLowerCase().indexOf('msie') > -1;\n\n  if ((is_webkit || is_opera || is_ie) && document.getElementById && window.addEventListener) {\n    window.addEventListener('hashchange', function () {\n      var id = location.hash.substring(1),\n          element;\n\n      if (!/^[A-z0-9_-]+$/.test(id)) {\n        return;\n      }\n\n      element = document.getElementById(id);\n\n      if (element) {\n        if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {\n          element.tabIndex = -1;\n        }\n\n        element.focus();\n      }\n    }, false);\n  }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJpc193ZWJraXQiLCJuYXZpZ2F0b3IiLCJ1c2VyQWdlbnQiLCJ0b0xvd2VyQ2FzZSIsImluZGV4T2YiLCJpc19vcGVyYSIsImlzX2llIiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsIndpbmRvdyIsImFkZEV2ZW50TGlzdGVuZXIiLCJpZCIsImxvY2F0aW9uIiwiaGFzaCIsInN1YnN0cmluZyIsImVsZW1lbnQiLCJ0ZXN0IiwidGFnTmFtZSIsInRhYkluZGV4IiwiZm9jdXMiXSwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vd3AtY29udGVudC90aGVtZXMvZWhkL3Jlc291cmNlcy9qcy9wbHVnaW5zLWRldi9za2lwLWxpbmstZm9jdXMtZml4LmpzPzcyYjkiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIEZpbGUgc2tpcC1saW5rLWZvY3VzLWZpeC5qc1xyXG4gKlxyXG4gKiBIZWxwcyB3aXRoIGFjY2Vzc2liaWxpdHkgZm9yIGtleWJvYXJkIG9ubHkgdXNlcnMuXHJcbiAqIFRoaXMgaXMgdGhlIHNvdXJjZSBmaWxlIGZvciB3aGF0IGlzIG1pbmlmaWVkIGluIHRoZSBhc3RyYV9za2lwX2xpbmtfZm9jdXNfZml4KCkgUEhQIGZ1bmN0aW9uLlxyXG4gKlxyXG4gKiBMZWFybiBtb3JlOiBodHRwczovL2dpdGh1Yi5jb20vQXV0b21hdHRpYy9fcy9wdWxsLzEzNlxyXG4gKlxyXG4gKiBAcGFja2FnZSBBc3RyYVxyXG4gKi9cclxuXHJcbiggZnVuY3Rpb24oKSB7XHJcblx0dmFyIGlzX3dlYmtpdCA9IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKCAnd2Via2l0JyApID4gLTEsXHJcblx0ICAgIGlzX29wZXJhICA9IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKCAnb3BlcmEnICkgPiAtMSxcclxuXHQgICAgaXNfaWUgICAgID0gbmF2aWdhdG9yLnVzZXJBZ2VudC50b0xvd2VyQ2FzZSgpLmluZGV4T2YoICdtc2llJyApID4gLTE7XHJcblxyXG5cdGlmICggKCBpc193ZWJraXQgfHwgaXNfb3BlcmEgfHwgaXNfaWUgKSAmJiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCAmJiB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciApIHtcclxuXHRcdHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCAnaGFzaGNoYW5nZScsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgaWQgPSBsb2NhdGlvbi5oYXNoLnN1YnN0cmluZyggMSApLFxyXG5cdFx0XHRcdGVsZW1lbnQ7XHJcblxyXG5cdFx0XHRpZiAoICEgKCAvXltBLXowLTlfLV0rJC8udGVzdCggaWQgKSApICkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cclxuXHRcdFx0ZWxlbWVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBpZCApO1xyXG5cclxuXHRcdFx0aWYgKCBlbGVtZW50ICkge1xyXG5cdFx0XHRcdGlmICggISAoIC9eKD86YXxzZWxlY3R8aW5wdXR8YnV0dG9ufHRleHRhcmVhKSQvaS50ZXN0KCBlbGVtZW50LnRhZ05hbWUgKSApICkge1xyXG5cdFx0XHRcdFx0ZWxlbWVudC50YWJJbmRleCA9IC0xO1xyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0ZWxlbWVudC5mb2N1cygpO1xyXG5cdFx0XHR9XHJcblx0XHR9LCBmYWxzZSApO1xyXG5cdH1cclxufSkoKTtcclxuIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBLENBQUUsWUFBVztFQUNaLElBQUlBLFNBQVMsR0FBR0MsU0FBUyxDQUFDQyxTQUFWLENBQW9CQyxXQUFwQixHQUFrQ0MsT0FBbEMsQ0FBMkMsUUFBM0MsSUFBd0QsQ0FBQyxDQUF6RTtFQUFBLElBQ0lDLFFBQVEsR0FBSUosU0FBUyxDQUFDQyxTQUFWLENBQW9CQyxXQUFwQixHQUFrQ0MsT0FBbEMsQ0FBMkMsT0FBM0MsSUFBdUQsQ0FBQyxDQUR4RTtFQUFBLElBRUlFLEtBQUssR0FBT0wsU0FBUyxDQUFDQyxTQUFWLENBQW9CQyxXQUFwQixHQUFrQ0MsT0FBbEMsQ0FBMkMsTUFBM0MsSUFBc0QsQ0FBQyxDQUZ2RTs7RUFJQSxJQUFLLENBQUVKLFNBQVMsSUFBSUssUUFBYixJQUF5QkMsS0FBM0IsS0FBc0NDLFFBQVEsQ0FBQ0MsY0FBL0MsSUFBaUVDLE1BQU0sQ0FBQ0MsZ0JBQTdFLEVBQWdHO0lBQy9GRCxNQUFNLENBQUNDLGdCQUFQLENBQXlCLFlBQXpCLEVBQXVDLFlBQVc7TUFDakQsSUFBSUMsRUFBRSxHQUFHQyxRQUFRLENBQUNDLElBQVQsQ0FBY0MsU0FBZCxDQUF5QixDQUF6QixDQUFUO01BQUEsSUFDQ0MsT0FERDs7TUFHQSxJQUFLLENBQUksZ0JBQWdCQyxJQUFoQixDQUFzQkwsRUFBdEIsQ0FBVCxFQUF3QztRQUN2QztNQUNBOztNQUVESSxPQUFPLEdBQUdSLFFBQVEsQ0FBQ0MsY0FBVCxDQUF5QkcsRUFBekIsQ0FBVjs7TUFFQSxJQUFLSSxPQUFMLEVBQWU7UUFDZCxJQUFLLENBQUksd0NBQXdDQyxJQUF4QyxDQUE4Q0QsT0FBTyxDQUFDRSxPQUF0RCxDQUFULEVBQTZFO1VBQzVFRixPQUFPLENBQUNHLFFBQVIsR0FBbUIsQ0FBQyxDQUFwQjtRQUNBOztRQUVESCxPQUFPLENBQUNJLEtBQVI7TUFDQTtJQUNELENBakJELEVBaUJHLEtBakJIO0VBa0JBO0FBQ0QsQ0F6QkQiLCJmaWxlIjoiLi93cC1jb250ZW50L3RoZW1lcy9laGQvcmVzb3VyY2VzL2pzL3BsdWdpbnMtZGV2L3NraXAtbGluay1mb2N1cy1maXguanMuanMiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./wp-content/themes/ehd/resources/js/plugins-dev/skip-link-focus-fix.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./wp-content/themes/ehd/resources/js/plugins-dev/skip-link-focus-fix.js"]();
/******/ 	
/******/ })()
;