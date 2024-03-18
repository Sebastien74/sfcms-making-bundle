/** Making bundle */
const imageComparisonSliders = document.querySelectorAll('[data-component="image-comparison-slider"]')
if (imageComparisonSliders.length > 0) {
    import(/* webpackPreload: true */ './components/making').then(({default: comparison}) => {
        new comparison(imageComparisonSliders)
    }).catch(error => console.error(error.message));
}