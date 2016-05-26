/**
 * This script handles functions of the main search index page.
 */

//Enables the random rotating artifact and adds the "js" class to disable no-js CSS styles
$('html').addClass('randArtRotating');
$('html').addClass('js');

//Hides or shows the specified control depending on the state of the "On Display" checkbox
function toggleVisibility(controlId) {
    var control = document.getElementById(controlId);
    //if(document.search.d.checked == true){
    if ($("#d").is(":checked")) {
        control.style.visibility = "visible";
    } else {
        control.style.visibility = "visible";
    }
}

$(document).ready(function () {

    //Setup the click handler for the "Show More Options" button. Toggles the visibility of the extra search options.
    $('#toggleAdv').click(function () {
        if ($('#advancedSearch').is(':visible')) {
            //Hide it
            $('#advancedSearch').hide('fast');
            $(this).html('<i class="icon-chevron-down"></i> Show More Options');
            $(this).removeClass('secondary')
        } else {
            //Show it
            $('#advancedSearch').show('fast');
            $(this).html('<i class="icon-chevron-up"></i> Hide More Options');
            $(this).addClass('secondary')
            //and hide the Help Text
            $('#examples').hide('fast');
            $('#toggleHlp').html('<i class="icon-info-sign"></i> Show Search Help');
            $('#toggleHlp').removeClass('secondary')
        }
		return false;
    });

    //Setup the click handler for the "Show Search Help" button. Toggles the visibility of the search help text.
	//Probably want to remove this whole thing if we turn the Search Help toggle into a modal.
    $('#toggleHlp').click(function () {
        if ($('#examples').is(':visible')) {
            //Hide it
            $('#examples').hide('fast');
            $(this).html('<i class="icon-info-sign"></i> Show Search Help');
            $(this).removeClass('secondary')
        } else {
            //Show it
            $('#examples').show('fast');
            $(this).html('<i class="icon-info-sign"></i> Hide Search Help');
            $(this).addClass('secondary')
			//and hide the Search Options
            $('#advancedSearch').hide('fast');
            $('#toggleAdv').html('<i class="icon-chevron-down"></i> Show More Options');
            $('#toggleAdv').removeClass('secondary')
        }
		return false;
    });



    //Initially hide the gallery select element
    toggleVisibility('selectGallery')
    //Re-setup the random artifact rotating thingy
    $('html').removeClass('randArtRotating');
    $("#SMrotating").html('');
    $("#SMrotating").rotatingElement({
        'enableRotation': true,
        'imageFolder': '/elements/random/',
        'refreshInterval': 15000,
        'animationLength': 2000,
        'preload': true,
        'fadeInAnimation': {opacity: 1.0},
        'fadeOutAnimation': {opacity: 0.0}
    });

    //Create future handlers for the facet side-panel's "More" button.
    var more = function () {
        //Show the hidden siblings of the "More" button
        $(this).parent().siblings('.hiddenFacet').show(800).removeClass('hiddenFacet').addClass('unhiddenFacet');
        //Change the "More" button into the "Less" button
        $(this).addClass('less').removeClass('more');
        $(this).html('[Less]');
        //Remove this click handler and assign the opposite one
        $(this).unbind('click');
        $(this).click(less);
    };

    //Create future handlers for the facet side-panel's "Less" button.
    var less = function () {
        //Hide the un-hidden siblings of the "Less" button
        $(this).parent().siblings('.unhiddenFacet').hide(800).removeClass('unhiddenFacet').addClass('hiddenFacet');
        //Change the "Less" button into the "More" button
        $(this).addClass('more').removeClass('less');
        $(this).html('[More]');
        //Remove this click handler and assign the opposite one
        $(this).unbind('click');
        $(this).click(more);
    };

    //Assign the click handler to the facet's "More" button
    $(".more").click(more);

    //If the facets sidebar is present, add a margin to the record list
    if ($("#leftSidebarOuter").length > 0) {
        $(".records").css('margin-left', $("#leftSidebarOuter").width() + 20);
    }

});
