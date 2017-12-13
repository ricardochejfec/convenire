
$('.mytab').click(function() {
    event.preventDefault();
    var f = $('#footer');
   	f.css({position:'absolute'});
   	
});

$(document).on("click", '#myothertab',function() {


    var f = $('#footer');
    f.css({position:'static'});
	
		if ($(document).height() <= $(window).height()) {
		alert("hey");

   		var f = $('#footer');
   		f.css({position:'absolute'});
		}
        
});


$(document).ready(function(){


   if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });

    var arr = window.location.pathname.split('/');
    var choice = '';
    
    

	$("#locBtn").click(function(){
		
		$('#locPoll li input').each(function(i)
		{
			
			if($(this).is(':checked')) {choice = this.labels[0].innerText;}
   	
		});
        $.ajax({url: "/updatelocationpoll", 
        		dataType: "json",
        		data : { eventId : arr[2] , address: choice  , email : localStorage.getItem('username')},
        		success: function(data){
            		
        	var sum = 0;
        	for (var i = 0 ; i< data.length; i++){

				sum = sum + parseInt(data[i]["votes"]);			
			}
        	var newHtml = "";
			for (var i = 0 ; i< data.length; i++){
				var perc =  (parseInt(data[i]["votes"])/sum)*100;
				newHtml = newHtml + '<li class="list-group-item">' + 
									'<div class="radio">' +  
                                                   '<p>'+
                                                        '<span class="glyphicon glyphicon-circle-arrow-right"></span>' +  "    " +
                                                        data[i]["address"]+ "						"  +  '<a style="font-weight:bold;">' + parseInt(perc) + "%"  + "</a>" +
                                                    '</p>' +
                                                '</div>' +
                                                '</li>';		
				
			}
			$( "#LocList" ).html(newHtml);
        
        }});
    });
    $("#timeBtn").click(function(){
		var timearr = [];
		$('#timePoll li input').each(function(i)
		{
			
			if($(this).is(':checked')) {
				var date = this.labels[0].innerText.substring(0,11);
				var start = this.labels[0].innerText.substring(12,17);
				var end = this.labels[0].innerText.substring(19,);


				var timechecked = {date: date, start: start, end: end};
				timearr.push(timechecked);
			}
   	
		});
        $.ajax({url: "/updatetimeepoll", 
        		dataType: "json",
        		data : { eventId : arr[2] , timearr: timearr  , email : localStorage.getItem('username')},
        		success: function(data){
            		
         	var sum = 0;
         	for (var i = 0 ; i< data.length; i++){

			 	sum = sum + parseInt(data[i]["votes"]);			
			 }
         	var newHtml = "";
			 for (var i = 0 ; i< data.length; i++){
			 	var perc =  (parseInt(data[i]["votes"])/sum)*100;
			 	newHtml = newHtml + '<li class="list-group-item">' + 
			 						'<div class="radio">' +  
                                                    '<p>'+
                                                         '<span class="glyphicon glyphicon-circle-arrow-right"></span>' +  "    " +
                                                         data[i]["date"]+ " "  + data[i]["StartTime"] + "-" + data[i]["EndTime"] + " "+ '<a style="font-weight:bold;">' + parseInt(perc) + "%"  + "</a>" +
                                                     '</p>' +
                                                 '</div>' +
                                                 '</li>';		
				
			 }
			 $( "#timeList" ).html(newHtml);
        
        }});
    });
    var btn = document.getElementById("MsgBtn");

    $(btn).click(function(){

    	var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; 
		var yyyy = today.getFullYear();
		var ss= today.getSeconds();
		var message = $('#textSent').val();
		
		if(dd<10) {
		    dd = '0'+dd
		} 
		
		if(mm<10) {
		    mm = '0'+mm
		} 
		
		var date = yyyy + '-' + mm + '-' + dd;
		var time = today.getHours() + ':' + today.getMinutes() + ":" + ss +":" + today.getMilliseconds();

    	$.ajax({url: "/messageToChat", 
        		dataType: "json",
        		data : {eventId : arr[2], message : message, date: date, time:time  , email : localStorage.getItem('username')},
        		success:function(data){
        }});
    });


    
    function fetchdata(){

  	
		$.ajax({
		 	url: '/chatUpdate',
		 	type: 'get',
		 	dataType: 'json',
		 	data: {eventId: arr[2]},
		 	success: function(data){

		 		var newHtml = "";
				for (var i = 0 ; i< data.length; i++){
			 	
			 		newHtml = newHtml + '<div class="media msg ">'+
                                              '<a class="pull-left" href="#"></a>'+
                                              '<div class="media-body">' +
                                                  '<small class="pull-right time"><i class="fa fa-clock-o"></i> '+ data[i]['time'] +'</small>'+
                                                  '<h5 class="media-heading">'+ data[i]['email'] +'</h5>'+
                                                  '<small class="col-lg-10">'+ data[i]['comment'] +'</small>'+
                                              '</div>'+
                                          '</div>';		
				
			 }
			 newHtml= newHtml + '<div class="media msg"><a class="pull-left" href="#"></a></div>';
			 $( "#chat" ).html(newHtml);


		 	},
		 	complete:function(data){
		 	 setTimeout(fetchdata,1000);
		 	}
		});
	}


	setTimeout(fetchdata,1000);

});

$(window).on('load resize scroll', function() {
    var f = $('#footer');

    if ($(document.body).height() > $(window).height()) {
        f.css({position:'static'});
    }
    var arr = window.location.pathname.split('/');

    $.ajax({
		 	url: '/chatUpdate',
		 	type: 'get',
		 	dataType: 'json',
		 	data: {eventId: arr[2]},
		 	success: function(data){

		 		var newHtml = "";
				for (var i = 0 ; i< data.length; i++){
			 	
			 		newHtml = newHtml + '<div class="media msg ">'+
                                              '<a class="pull-left" href="#"></a>'+
                                              '<div class="media-body">' +
                                                  '<small class="pull-right time"><i class="fa fa-clock-o"></i> '+ data[i]['time'] +'</small>'+
                                                  '<h5 class="media-heading">'+ data[i]['email'] +'</h5>'+
                                                  '<small class="col-lg-10">'+ data[i]['comment'] +'</small>'+
                                              '</div>'+
                                          '</div>';		
				
			 }
			 newHtml= newHtml + '<div class="media msg"><a class="pull-left" href="#"></a></div>';
			 $( "#chat" ).html(newHtml);


		 	}
		 	
		});

});

$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
});



