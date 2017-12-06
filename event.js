
$(window).on('load resize scroll', function() {
 //    var f = $('#footer');
	// f.css({position:'static'});
 //    if ($(document.body).height() <= $(window).height()) {
 //       	f.css({position:'absolute'});
 //    } 
	 $('.mytab').click(function() {
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



