
$(window).on('load resize scroll', function() {
    var f = $('#footer');

    if ($(document.body).height() > $(window).height()) {
        f.css({position:'static'});
    }
});



