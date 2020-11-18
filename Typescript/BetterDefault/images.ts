// npm install baguettebox.js --save
const baguetteBox = require('baguettebox.js/src/baguetteBox');

document.addEventListener('DOMContentLoaded', () => {

    let galleries = document.querySelectorAll('[data-gallery]') as NodeListOf<HTMLElement>;
    galleries.forEach((element: HTMLElement) => {
        baguetteBox.run('[data-gallery="'+element.dataset.gallery+'"]');
    });

    let bodyEdit = document.querySelector('body.body-edit');
    let pageEdit = document.querySelector('body.page-edit');
    if (null !== bodyEdit || null !== pageEdit) {
        document.addEventListener('bindElement', (event: any) => {
            let element = document.querySelector('[data-uuid="'+event.detail.elementUuid+'"]') as HTMLElement;
            let galleryId = 'gallery' in element.dataset ?  element.dataset.gallery : event.detail.elementUuid;
            baguetteBox.run('[data-gallery="'+galleryId+'"]');
        });
    }
});
