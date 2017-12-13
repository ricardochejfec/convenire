$(document).ready(function () {

    
    $(window).on('resize',function() {
        if ($(document).height() > $(window).height()) {
            var f = $('#footer');
            f.css({position: "static"});
        } else {
            var f = $('#footer');
            f.css({position: "absolute"});
        }
    });

    $(document).ready(function() {
         $(window).trigger('resize');
        });



    $('.carousel').carousel('cycle');



    $("#loginform").submit(function(){
        alert("bitch");
    	localStorage.setItem('username',  document.getElementById("username").value);

    });


});