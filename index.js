$(document).ready(function () {
    $('.carousel').carousel({
        interval: 2000;
    });

    $('.carousel').carousel('cycle');

    $('window').click(function() {
            event.preventDefault();
            var f = $('#footer');
		   	f.css({position:'absolute'});
		   	
	    });

	 $('#myothertab').click(function() {
            event.preventDefault();
            var f = $('#footer');
            f.css({position:'static'});
	    });
});