/*
References: https://www.sitepoint.com/community/t/how-to-keep-accordion-drop-down-menu-open-when-opening-a-new-page/9033
*/

$(document).ready( function() {

    $('#submenu ul').each( function() {
        var currentURI = window.location.href; //the current page
        var links = $('a', this); //each links
        var collapse = true; //boolean value of collapse
        
        //Initializing #submenu
        //Walks through all the sub-item of #submenu and then its links
        for (var i = 0; i < links.size(); i++) {
            var elem = links.eq(i); //retrieving the element of each link
            var href = elem.attr('href'); //retrieving the the value of href 
            var hrefLength = href.length; //the length of each href
            var compareTo = currentURI.substr(-1*hrefLength); //extracts the string of the current page
            
            //If that href equals to the current page
            //then the dropdown/sub-item will remain open
            if (href == compareTo) {
                collapse = false;
                break;
            }
        };

        //If they do not equal 
        //then the dropdown/sub-item will hide all the links
        if (collapse) {
            $(this).hide();
        }
    });
    
    //Handles events in dropdown
    $("#submenu").delegate('span', 'click', function() {
        var ul = $(this).next('ul');

        if (ul.is(':visible')) {
            ul.slideUp(500);
        }
        
        else {
            $('#submenu ul').not(ul).slideUp(500);
            ul.slideDown(500);
        }
    });
});