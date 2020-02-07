/* This file defines the transitions/animations for pictures */
// All pics on the page. Automatically hidden by base.js
var pictures = document.getElementsByClassName("pic");

// For pauses
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Fade animation
async function fade(_holdTime) {
    const fadeInAnim = "fadein 2s"; // Defined in base.css
    const fadeOutAnim = "fadeout 2s";
    const fadeTimeMS = 2000; // Fade time in milliseconds for sleep. Should match CSS.
    const betweenTime = 0;

    for (var i = 0; i < pictures.length; i++) {
        // fade-in
        pictures[i].style.opacity = 0;
        pictures[i].style.animation = fadeInAnim;
        await sleep(fadeTimeMS);
        // hold
        pictures[i].style.opacity = 1;
        await sleep(_holdTime);
        // fade-out
        pictures[i].style.animation = fadeOutAnim;
        await sleep(fadeTimeMS);
        pictures[i].style.opacity = 0;
        // optional hold between pics
        await sleep(betweenTime);
    }

    // call back loop. fix into loop 'too much recursion'.
    fade(_holdTime);
}

if (pictures.length > 0) {
    fade(10000); // hold for 10 secs
}