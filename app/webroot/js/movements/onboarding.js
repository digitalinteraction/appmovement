/* Movement Onboarding */

var panelCount = 3;

$(document).ready(function() {

	$('#continue-button').click(function() {
		
		submitForm();

		return false;
	});

	attachHandlers();

});

function addPanel() {

	panelCount++; // Increment panel count

	// Add panel
	var panelHtml = '<div class="col-sm-4"><div class="user-panel" data-index="' + panelCount + '"><input type="text" class="name-field" placeholder="Name"><input type="text" class="email-field" placeholder="Email"></div></div>';

	$(panelHtml).hide().appendTo("#panel-container").fadeIn(500);

	attachHandlers();
}

function attachHandlers() {

	$('.name-field').keyup(function() {
		if ((emptyPanels() == 0) && (panelCount < 6)) {
			addPanel();
		}
	});

	$('.email-field').keyup(function() {
		if ((emptyPanels() == 0) && (panelCount < 6)) {
			addPanel();
		}
	});

	$('.name-field').blur(function() {
		checkProgress();
	});

	$('.email-field').blur(function() {
		checkProgress();
	});
}

function emptyPanels() {

	var emptyPanelCount = 0;

	for (var i = 0; i <= panelCount; i++) {

		if (($('.user-panel:eq(' + i + ') .email-field').val() == '') || ($('.user-panel:eq(' + i + ') .name-field').val() == '')) {
			
			emptyPanelCount++;
		}

	};

	return emptyPanelCount;
}

function checkProgress() {

	var progress = 0;

	for (var i = 0; i <= panelCount; i++) {

		var name = $('.user-panel:eq(' + i + ') .name-field').val();
		var email = $('.user-panel:eq(' + i + ') .email-field').val();

		if (checkEmail(email) && (name != '')) {
			if (progress < 100) {
				progress = progress + (100 / 3);
			}
		}
	};

	if ((progress > 0) && ($('.progress-bar').attr('aria-valuenow') == 0)) {

		// Show progress bar
		$('#progress-bar').slideDown(500);

		setTimeout(function() {
			$('.progress-bar').css('width', progress + '%');
			$('.progress-bar').attr('aria-valuenow', progress);
		},600);

	} else {

		$('.progress-bar').css('width', progress + '%');
		$('.progress-bar').attr('aria-valuenow', progress);
	
	}

	if (progress >= 100) {

		// Min required passed
		$('#continue-button').addClass('btn-success');
		$('#continue-button').removeClass('btn-default');

	} else {

		$('#continue-button').addClass('btn-default');
		$('#continue-button').removeClass('btn-success');


	}
}

function checkEmail(passedEmail) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

	var email = String(passedEmail);

	if(email.match(re) == null) {
		return false;
	}

	return true;
}

function submitForm() {

	var userArray = [];

	for (var i = 0; i <= panelCount; i++) {

		var name = $('.user-panel:eq(' + i + ') .name-field').val();
		var email = $('.user-panel:eq(' + i + ') .email-field').val();

		if (checkEmail(email) && (name != '')) {
			
			var user = new Object;
			user.name = name;
			user.email = email;
			userArray.push(user);
		}

	};

	var json = JSON.stringify(userArray);

	$('#form-data').val(json);
	$('#data-form').submit();

}