$(document).ready(function () {
    

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
    $("#loginform").submit(function(){

    	localStorage.setItem('username',  document.getElementById("username").value);

    });

    $("#loginform").click(function(){
        
                localStorage.setItem('username',  document.getElementById("username").value);
        
            });
});