"use strict";

/**
 * Toggle Mobile menu
 * @returns {any}
 */
let toggleMenu = function(){
    document.getElementById("nav").classList.toggle("responsive");
    document.getElementById("footer").classList.toggle("responsive");
    document.getElementById("toggle").classList.toggle("responsive");
};


document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("toggle").addEventListener("click", toggleMenu);
});

/**
 * Thanks to NullDev!
 * https://github.com/NullDev
 */
