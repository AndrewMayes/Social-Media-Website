$(document).ready( function() {
    
    // initialize accordion
    $('#submenu ul').each( function() {
        var currentURI = window.location.href;
        var links = $('a', this);
        var collapse = true;
        for (var i = 0; i < links.size(); i++) {
            var elem = links.eq(i);
            var href = elem.attr('href');
            var hrefLength = href.length;
            var compareTo = currentURI.substr(-1*hrefLength);
            
            if (href == compareTo) {
                collapse = false;
                break;
            }
        };
        if (collapse) {
            $(this).hide();
        }
    });
    
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