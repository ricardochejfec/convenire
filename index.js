$(document).ready(function () {
    

    $('.carousel').carousel('cycle');

    $("#loginbtn").click(function(){

    	localStorage.setItem('username',  document.getElementById("username").value);

    });
});