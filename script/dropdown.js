/*
References: https://www.sitepoint.com/community/t/how-to-keep-accordion-drop-down-menu-open-when-opening-a-new-page/9033
*/

$(document).ready( function() {
    
    //Initializing #submenu
    $('#submenu ul').each( function() {
        var url = $('a', this); //each links
        var hide = true; //boolean value of hide variable

        //Walks through all the sub-item of #submenu and then its links
        for (var i = 0; i < url.size(); i++) {
            var href = (url.eq(i)).attr('href'); //retrieving the the value of attribute href 
            var href_comp = (window.location.href).substr(-(href.length)); //extracts the string of the current page
            
            //If that href equals to the current page
            //then the dropdown/sub-item will remain open
            if (href == href_comp) {
                hide = false;
                break;
            }
        };

        //If they do not equal 
        //then the dropdown/sub-item will hide all the links
        if (hide) {
            $(this).hide();
        }
    });
    
    //Handles events in dropdown
    $("#submenu").delegate('span', 'click', function() {
        var ul = $(this).next('ul');
        if (ul.is(':visible')) {
            ul.slideUp(500);
        } else {
            $('#submenu ul').not(ul).slideUp(500);
            ul.slideDown(500);
        }
    });
});
