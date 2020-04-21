// npm install baguettebox.js --save
const baguetteBox = require('baguettebox.js/src/baguetteBox');

document.addEventListener('DOMContentLoaded', () => {

    let galleries = document.querySelectorAll('[data-gallery]') as NodeListOf<HTMLElement>;
    galleries.forEach((element: HTMLElement) => {
        baguetteBox.run('[data-gallery="'+element.dataset.gallery+'"]');
    });

    let bodyEdit = document.querySelector('body.page-edit');
    if (null !== bodyEdit) {
        document.addEventListener('bindElement', (event: any) => {
            baguetteBox.run('[data-gallery="'+event.detail.elementUuid+'"]');
        });
    }
});
