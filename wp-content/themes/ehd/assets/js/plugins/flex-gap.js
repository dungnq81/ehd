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

/***/ "./wp-content/themes/ehd/resources/js/plugins-dev/flex-gap.js":
/*!********************************************************************!*\
  !*** ./wp-content/themes/ehd/resources/js/plugins-dev/flex-gap.js ***!
  \********************************************************************/
/***/ (function() {

eval("/**\r\n * https://github.com/Modernizr/Modernizr/blob/master/feature-detects/css/flexgap.js\r\n *\r\n */\n\n/*create flex container with row-gap set*/\nvar flex = document.createElement(\"div\");\nflex.style.display = \"flex\";\nflex.style.flexDirection = \"column\";\nflex.style.rowGap = \"1px\";\n/*create two, elements inside it*/\n\nflex.appendChild(document.createElement(\"div\"));\nflex.appendChild(document.createElement(\"div\"));\n/*append to the DOM (needed to obtain scrollHeight)*/\n\ndocument.body.appendChild(flex);\n/*flex container should be 1px high from the row-gap*/\n\nvar isSupported = flex.scrollHeight === 1;\nflex.parentNode.removeChild(flex);\n\nif (isSupported) {\n  document.documentElement.classList.add(\"flex-gap\");\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6WyJmbGV4IiwiZG9jdW1lbnQiLCJjcmVhdGVFbGVtZW50Iiwic3R5bGUiLCJkaXNwbGF5IiwiZmxleERpcmVjdGlvbiIsInJvd0dhcCIsImFwcGVuZENoaWxkIiwiYm9keSIsImlzU3VwcG9ydGVkIiwic2Nyb2xsSGVpZ2h0IiwicGFyZW50Tm9kZSIsInJlbW92ZUNoaWxkIiwiZG9jdW1lbnRFbGVtZW50IiwiY2xhc3NMaXN0IiwiYWRkIl0sInNvdXJjZXMiOlsid2VicGFjazovLy8uL3dwLWNvbnRlbnQvdGhlbWVzL2VoZC9yZXNvdXJjZXMvanMvcGx1Z2lucy1kZXYvZmxleC1nYXAuanM/OWMzNiJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogaHR0cHM6Ly9naXRodWIuY29tL01vZGVybml6ci9Nb2Rlcm5penIvYmxvYi9tYXN0ZXIvZmVhdHVyZS1kZXRlY3RzL2Nzcy9mbGV4Z2FwLmpzXHJcbiAqXHJcbiAqL1xyXG5cclxuLypjcmVhdGUgZmxleCBjb250YWluZXIgd2l0aCByb3ctZ2FwIHNldCovXHJcbmxldCBmbGV4ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcclxuZmxleC5zdHlsZS5kaXNwbGF5ID0gXCJmbGV4XCI7XHJcbmZsZXguc3R5bGUuZmxleERpcmVjdGlvbiA9IFwiY29sdW1uXCI7XHJcbmZsZXguc3R5bGUucm93R2FwID0gXCIxcHhcIjtcclxuXHJcbi8qY3JlYXRlIHR3bywgZWxlbWVudHMgaW5zaWRlIGl0Ki9cclxuZmxleC5hcHBlbmRDaGlsZChkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwiZGl2XCIpKTtcclxuZmxleC5hcHBlbmRDaGlsZChkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwiZGl2XCIpKTtcclxuXHJcbi8qYXBwZW5kIHRvIHRoZSBET00gKG5lZWRlZCB0byBvYnRhaW4gc2Nyb2xsSGVpZ2h0KSovXHJcbmRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQoZmxleCk7XHJcblxyXG4vKmZsZXggY29udGFpbmVyIHNob3VsZCBiZSAxcHggaGlnaCBmcm9tIHRoZSByb3ctZ2FwKi9cclxubGV0IGlzU3VwcG9ydGVkID0gZmxleC5zY3JvbGxIZWlnaHQgPT09IDE7XHJcbmZsZXgucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChmbGV4KTtcclxuaWYoaXNTdXBwb3J0ZWQpIHtcclxuICAgIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5jbGFzc0xpc3QuYWRkKFwiZmxleC1nYXBcIik7XHJcbn0iXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0EsSUFBSUEsSUFBSSxHQUFHQyxRQUFRLENBQUNDLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBWDtBQUNBRixJQUFJLENBQUNHLEtBQUwsQ0FBV0MsT0FBWCxHQUFxQixNQUFyQjtBQUNBSixJQUFJLENBQUNHLEtBQUwsQ0FBV0UsYUFBWCxHQUEyQixRQUEzQjtBQUNBTCxJQUFJLENBQUNHLEtBQUwsQ0FBV0csTUFBWCxHQUFvQixLQUFwQjtBQUVBOztBQUNBTixJQUFJLENBQUNPLFdBQUwsQ0FBaUJOLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1QixLQUF2QixDQUFqQjtBQUNBRixJQUFJLENBQUNPLFdBQUwsQ0FBaUJOLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1QixLQUF2QixDQUFqQjtBQUVBOztBQUNBRCxRQUFRLENBQUNPLElBQVQsQ0FBY0QsV0FBZCxDQUEwQlAsSUFBMUI7QUFFQTs7QUFDQSxJQUFJUyxXQUFXLEdBQUdULElBQUksQ0FBQ1UsWUFBTCxLQUFzQixDQUF4QztBQUNBVixJQUFJLENBQUNXLFVBQUwsQ0FBZ0JDLFdBQWhCLENBQTRCWixJQUE1Qjs7QUFDQSxJQUFHUyxXQUFILEVBQWdCO0VBQ1pSLFFBQVEsQ0FBQ1ksZUFBVCxDQUF5QkMsU0FBekIsQ0FBbUNDLEdBQW5DLENBQXVDLFVBQXZDO0FBQ0giLCJmaWxlIjoiLi93cC1jb250ZW50L3RoZW1lcy9laGQvcmVzb3VyY2VzL2pzL3BsdWdpbnMtZGV2L2ZsZXgtZ2FwLmpzLmpzIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./wp-content/themes/ehd/resources/js/plugins-dev/flex-gap.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./wp-content/themes/ehd/resources/js/plugins-dev/flex-gap.js"]();
/******/ 	
/******/ })()
;