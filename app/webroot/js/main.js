/** Main Javascript **/

$(document).ready(function() {

	// Fade video in after loading
	$('.video-wrapper iframe').delay(1500).fadeIn(1000);

	// Wait 5 seconds before hiding flash message
	$('#flashMessage').delay(4000).slideUp(500);
	$('#goodMessage').delay(4000).slideUp(500);
	$('#badMessage').delay(4000).slideUp(500);

	if ($(this).width() >= 768) {
		$('.feed-back-box').show();
	} else {
		$('.feed-back-box').hide();
	}
	
	$(window).resize(function() {
	  
		if ($(this).width() >= 768) {
			$('.feed-back-box').show();
		} else {
		  	$('.feed-back-box').hide();
		}

	});

	$('.feed-back-box').click(function(){
		$('#feedback-modal').modal('show');
	});

	$('.feed-back-link').click(function(){
		$('#feedback-modal').modal('show');
	});

	$('#feedback-modal-submit').click(function(){
		var data = {};
        data.email = $('#feedback-email').val();
        data.comment = $("textarea#feedback-comment").val();
        data.url = window.location.pathname;

        $.ajax({
           type: "POST",
           url: base_url + 'feedback/submit',
           data: data,
           success: function(data)
           {
               $('#feedback-modal').modal('hide');
           }
        });
	});

});

// Update character counter
function textCounter(textarea, counterID, maxLen, blur) {
    var textString = $(textarea).val();
    var textLength = (maxLen - textString.length);
	if (textLength < 0) {
		$(textarea).val(textString.substring(0,maxLen));
		textLength = 0;
		textString = $(textarea).val();
		$('#' + counterID).css('color', '#f00');
	} else {
		$('#' + counterID).css('color', '#999');
	}
	var textCount;
	if (blur) {
		textCount = '';
	} else {
		textCount = textString.length + ' / ' + maxLen;
	}
	$('#' + counterID).html(textCount);
}

// Check cookies

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
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