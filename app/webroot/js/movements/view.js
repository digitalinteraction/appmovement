/* View Movement */

var tour;

$(document).ready(function() {
	
	addHandlers();

	add_update_delete_handler();

	checkTourCookie();

	// Instance the tour
	tour = new Tour({
	  steps: [
	  {
	    element: ".movement-container",
	    title: "Movement Description",
	    content: "This is a description of the movement and what it aims to accomplish.",
	    placement: "top"
	  },
	  {
	    element: ".phase-bar",
	    title: "Movement Phase",
	    content: "The phase bar shows the status of the movement.",
	    placement: "bottom"
	  },
	  {
	    element: "#stats-sidebar",
	    title: "Statistics",
	    content: "These are the movement statistics - promote this movement through social media.",
	    placement: "left"
	  },
	  {
	    element: "#support-btn",
	    title: "Support",
	    content: "Help this movement reach its goal by clicking the support button.",
	    placement: "left"
	  },
	  {
	    title: "Tour Complete",
	    content: "You have completed the tour!"
	  }
	],
	backdrop: true,
	storage: false,
	template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-info btn-block' style='' id='continue-btn' data-role='next'>Continue</button><button class='btn btn-default' style='display:none' data-role='end'>End tour</button></div></div>",
	onShown: function (tour) { checkIfLastStep(); }
	});

	// Initialize the tour
	tour.init();

	if (false) {
		if (!has_supported) {
			if (!$('.message').length) {
				if (show_tour) { tour.start(); }
			} else {
				setTimeout(function() { if ($(window).width() > 1200) { if (show_tour) { tour.start(); } } }, 4500);
			}
		}
	};

	$(".update-created-ago").timeago();
		
	$('.supporter_count_wrapper').tooltip('show').tooltip('hide');

	$('.btn-support-confirm').click(function(){
		confirm_support();
	});

	$('.count-up').counterUp({
		delay: 10, // the delay time in ms
		time: 1000 // the speed time in ms
	});
});

// Check if last step
function checkIfLastStep() {
	if (tour.getCurrentStep() == 3) {
		$('#continue-btn').html('End Tour');
	}
	if (tour.getCurrentStep() == 5) {
		tour.end();
		// if creator than show support modal
		if (is_creator && !has_supported) {
			$('#support-modal').modal('show');
		}
	}
	// console.log(tour.getCurrentStep());
}

// Check if the user has been shown the tour
function checkTourCookie() {

	if (show_tour == 0) {
		
		if (getCookie('shown_tour') == "") {
			show_tour = 1;
			setCookie('shown_tour', 1, 30);
		}

	} else {

		setCookie('shown_tour', 1, 30);

	}

	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toGMTString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
	    }
	    return "";
	}
}

// Check vote
function confirm_support() {

	$('.error_msgs').empty();

	var g_recaptcha_response = $('.g-recaptcha-response').val();

	$.getJSON(base_url + "confirm_support", {movement_id:movement_id, grecaptcha_response: g_recaptcha_response} , function(data) {
		if(data) {
			console.log(data);
			if(data.meta.success)
			{
				var new_count = parseInt($('#supporters-count').html()) + 1;
				var new_remaining = parseInt($('#remaining-supporters-count').html()) - 1;
				$('#supporters-count').html(new_count);
				$('#remaining-supporters-count').html(new_remaining);
				$('#temp-banner').show();
				$('.btn-support-confirm').html('<i class="fa fa-check-circle"></i> ' + lang.viewMovement.supported);

				if (movement_phase == 1)
				{
					$('.support-btn').hide();
					$('.temp-design-btn').css('display', 'block');
				} else {
					$('.support-btn').html('<i class="fa fa-check-circle"></i> ' + lang.viewMovement.supported);
				}
				
				setTimeout(function() {

					hideModal();
					loadChart(); // Reload supporters chart
					window.scrollTo(0,0); // Scroll to top of page

				}, 1500);
			}
			else
			{
				$.each(data.meta.errors, function(index){
					$('.error_msgs').append("<p>" + data.meta.errors[index] + "</p>");
				});
			}
		}
	});
}

// Display auth modal
function showAuthModal(movement_id) {

	$('#auth-modal').modal('show');
}

// Display download modal
function showDownloadModal() {

	$('#download-modal').modal('show');
}

// Display modal
function showModal(movement_id) {

	$.get(base_url + 'code', {id: movement_id}, function(data) {

		var data = $.parseJSON(data);
		
		if(data.meta)
		{
			$('#support-modal').fadeIn(300);

			$('#support-modal-backdrop').css('opactiy', 0.0).show().animate({'opacity':1.0}, 300);
		}
		else
		{
			alert('An error has ocurred loading data');
		}
	});
}

// Hide modal
function hideModal() {

	$('#support-modal').fadeOut(300);

	$('#support-modal-backdrop').css('opactiy', 1.0).animate({'opacity':0.0}, 300, function() { $(this).hide(); });
}

// Promote movement - adds user as promoter and displays promoter modal,
// pass in movement_id AND referral service (i.e. facebook, twitter etc)
function promoteMovement(movement_id) {
	$.get(base_url + 'promote/' + movement_id + '/add', {id: movement_id}, function(data) {

		var data = $.parseJSON(data);
		if(data.meta.success)
		{
			$('#reflink').val(data.response.ref_link);
		}
		else
		{
			alert('An error has ocurred loading data');
		}
	});
}

// Show share movement window
function shareMovement(windowUri)
{
	var centerWidth = (window.screen.width - 600) / 2;
    var centerHeight = (window.screen.height - 440) / 2;

    newWindow = window.open(windowUri, 'Share Movement', 'resizable=1,width=' + 600 + ',height=' + 440 + ',left=' + centerWidth + ',top=' + centerHeight);
    newWindow.focus();
    return newWindow.name;
}


// log share button click
function logShare(type)
{
	console.log(base_url + 'logs/share_button/' + movement_id + '/' + ref_link + '/' + type);
	$.ajax({
	   type: "GET",
	   url: base_url + 'logs/share_button/' + movement_id + '/' + ref_link + '/' + type,
	   success: function(data)
	   {
	   },
	   error: function(error)
	   {
	   	  console.log(error);
	   }
	});
}

function addHandlers() {

	$('#read-more-button').click(function() {
		$(this).hide();		
		$('.short-description').hide();
		$('.full-description').show();
	});

	$('#reflink').click(function() {$(this).select(); });

	$('#support-modal-close-button').click(function() {

		hideModal();

	});

	$('#update-button').click(function() {

		// Show update container

		$('#update-container').slideDown(300);

		$('#input-update').focus();

		$('html, body').animate({

	        scrollTop: ($("#update-container").offset().top - $('#navigation').height())
	    
	    }, 500);

	});

	$('#post-update-button').click(function() {

		// Post update
    	
		var data = {'data[MovementUpdate][text]': $('#input-update').val(), 'data[MovementUpdate][movement_id]': movement_id};

		$.ajax({
		   type: "POST",
		   url: base_url + 'updates/add',
		   data: data,
		   success: function(data)
		   {
		       var data = $.parseJSON(data);

		       $('#update-form-errors-list').empty();

		       if(data.meta.success)
		       {
		          // $('#comment-form-errors').hide();
		          $(data.response).hide().appendTo($('.movement-updates')).slideDown(300);

		          $(".update-created-ago").timeago();
		          $('#input-update').val('');
		          add_update_delete_handler();
		          $('#update-form-errors').hide();
		       }
		       else
		       {
		       	 // output errors
		       	 $('#update-form-errors').show();
		       	 $.each(data.errors, function(index, value){
		       	 	$('#update-form-errors-list').append('<li>' + value + '</li>');
		       	 });
		       }
		   },
		   error: function(error)
		   {
		   	  console.log(error);
		   }
		});

	});

	$('input[name="data[tags]"]').tagit({
	    readOnly: "true",
	    onTagClicked: function(event, ui) {
	        document.location = '../discover?query=' + ui.tagLabel;
	    }
	});
	
	var status = sprintf(lang.shareButton.status, movement_user_fullname, movement_title);

	$('.share-fb').click(function(){
		FB.ui({
		  method: 'share',
		  href: 'http://apmv.co/' + ref_link,
		}, function(response){});

		logShare('facebook');
		promoteMovement(movement_id);
	});

	$('.share-twitter').click(function(){
		var url = 'http://twitter.com/home?status=' + status + ' http://apmv.co/' + ref_link;
		console.log(url);
		shareMovement(url);
		logShare('twitter');
		promoteMovement(movement_id);
	});

	$('.share-googleplus').click(function(){
		var url = 'https://plus.google.com/share?url=' + status + ' http://apmv.co/' + ref_link;
		shareMovement(url);
		logShare('googleplus');
		promoteMovement(movement_id);
	});

	// encodeURI(ref_link)
	$('.share-email').click(function(){
		var url = 'mailto:?Subject=' + movement_title + '&Body=' + encodeURI(sprintf(lang.shareButton.email.body, movement_title, ref_link));
		window.location.href = url;
		logShare('email');
		promoteMovement(movement_id);
	});



}

function add_update_delete_handler() {

  $('.update-delete-button').unbind('click');

  $('.update-delete-button').click(function() {


    if (window.confirm("Delete update?")) {

      var update_id = $(this).attr('data-updateid');

      $.ajax({
           type: "POST",
           url: base_url + 'updates/delete/' + update_id,
           success: function(data)
           {
               var data = $.parseJSON(data);

               // console.log(data);

               if(data.meta.success)
               {
                  // remove update
                  $('#update-' + update_id).slideUp(300);
               }
               else
               {
               }
           },
           error: function(error)
           {
              console.log(error);
           }
      });

    }

  });

}