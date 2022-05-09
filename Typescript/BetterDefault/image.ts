// npm install intersection-observer --save
// npm install lozad --save
require('intersection-observer');
let lozad = require('lozad');
(window as any).imageObserver = lozad();

document.addEventListener('DOMContentLoaded', () => {
    (window as any).imageObserver.observe();
});

// Load lazy image after refresh in editor.
document.addEventListener('bindElement', () => {
    // Bugfix for Lozad.
    // @see https://github.com/ApoorvSaxena/lozad.js/pull/249
    const noScriptImages = document.querySelectorAll('.lozad noscript img') as NodeListOf<HTMLElement>;
    noScriptImages.forEach((img) => {
        img.remove();
    });

    (window as any).imageObserver.observe();
});
