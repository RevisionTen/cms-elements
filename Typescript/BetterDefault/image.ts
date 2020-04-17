// npm install intersection-observer --save
// npm install lozad --save
require('intersection-observer');
let lozad = require('lozad');
(window as any).imageObserver = lozad();

document.addEventListener('DOMContentLoaded', () => {
    (window as any).imageObserver.observe();

    // Load lazy image after refresh in editor.
    document.addEventListener('bindElement', () => {
        (window as any).imageObserver.observe();
    });
});
