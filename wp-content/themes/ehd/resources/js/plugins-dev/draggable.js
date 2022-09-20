/*! draggable - https://www.kirupa.com/html5/drag.htm */
import {nanoid} from "nanoid";

const draggable_container = [...document.querySelectorAll(".o_draggable")];
draggable_container.forEach((el, index) => {
    const _rand = nanoid(6);
    el.classList.add('draggable-' + _rand);
    let active = false, currentX, currentY, initialX, initialY, xOffset = 0, yOffset = 0;
    el.addEventListener("touchstart", dragStart, false);
    el.addEventListener("touchend", dragEnd, false);
    el.addEventListener("touchmove", drag, false); el.addEventListener("mousedown", dragStart, false);
    el.addEventListener("mouseup", dragEnd, false); el.addEventListener("mousemove", drag, false);
    function dragStart(e) {
        if (e.type === "touchstart") {
            initialX = e.touches[0].clientX - xOffset; initialY = e.touches[0].clientY - yOffset;
        } else {
            initialX = e.clientX - xOffset; initialY = e.clientY - yOffset;
        }
        active = true;
    }
    function dragEnd(e) {
        initialX = currentX; initialY = currentY;
        active = false;
    }
    function drag(e) {
        if (active) {
            e.preventDefault();
            if (e.type === "touchmove") {
                currentX = e.touches[0].clientX - initialX; currentY = e.touches[0].clientY - initialY;
            } else {
                currentX = e.clientX - initialX; currentY = e.clientY - initialY;
            }
            xOffset = currentX;
            yOffset = currentY;
            el.style.transform = "translate3d(" + currentX + "px, " + currentY + "px, 0)";
        }
    }
});