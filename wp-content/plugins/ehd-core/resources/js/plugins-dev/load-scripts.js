const timeout = 5000;
const loadScriptsTimer = setTimeout(loadScripts, timeout);
const userInteractionEvents = ["mouseover", "keydown", "touchstart", "touchmove", "wheel"];

userInteractionEvents.forEach(function (event) {
    window.addEventListener(event, triggerScriptLoader, {passive: !0})
});

function triggerScriptLoader() {
    loadScripts();
    clearTimeout(loadScriptsTimer);
    userInteractionEvents.forEach(function (event) {
        window.removeEventListener(event, triggerScriptLoader, {passive: !0})
    })
}

function loadScripts() {
    document.querySelectorAll("script[data-type='lazy']").forEach(function (elem) {
        elem.setAttribute("src", elem.getAttribute("data-src"));
        elem.removeAttribute("data-src");
        elem.removeAttribute("data-type");
    })
}