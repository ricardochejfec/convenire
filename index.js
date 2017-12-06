$(document).ready(function () {
    

    $('.carousel').carousel('cycle');

<<<<<<< HEAD
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
=======
    $("#loginbtn").click(function(){

    	localStorage.setItem('username',  document.getElementById("username").value);

    });
>>>>>>> 057b0653f70e3f268a439650670563f4062eca0f
});