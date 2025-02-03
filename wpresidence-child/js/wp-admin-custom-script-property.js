
$ = jQuery;

(function () {

    jQuery(document).ready(function($){
        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 1000);

        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 2000);

        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 3000);

        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 4000);

        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 5000);

        setTimeout(function(){
            hidePropertyType();
            hidePropertyNeighborhood();
            hidePropertyFeaturesAmenities();
            hidePropertyPropertyStatus();
            hidePropertyCountyOrState();
        }, 6000);
    });

    function hidePropertyType() {
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'Type';
        }).closest('.components-panel__body').hide();
    }

    function hidePropertyNeighborhood() {
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'Neighborhood';
        }).closest('.components-panel__body').hide();
    }

    function hidePropertyFeaturesAmenities() {
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'Features &amp; Amenities';
        }).closest('.components-panel__body').hide();
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'Features & Amenities';
        }).closest('.components-panel__body').hide();
    }

    function hidePropertyPropertyStatus() {
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'Property Status';
        }).closest('.components-panel__body').hide();
    }

    function hidePropertyCountyOrState() {
        $('button.components-panel__body-toggle').filter(function(){
            return $(this).text().trim() === 'County / State';
        }).closest('.components-panel__body').hide();
    }
    

})();







// jQuery(document).ready(function ($) {
//     // In Property Page - Hide elements with specific data-content attributes
//     const elementsToHide = [
//         // '[data-content="property_agent"]',
//         '[data-content="property_floor"]',
//         '[data-content="property_paid"]',
//         '[data-content="property_subunits"]',
//         '#property_map_trigger',
//         '#property_energy_trigger'
//     ];

//     // Iterate through the selectors and hide the elements
//     elementsToHide.forEach(selector => {
//         $(selector).hide();
//     });
// });
