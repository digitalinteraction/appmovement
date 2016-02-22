/* Start Movement */

$(document).ready(function() {
	
	// Setup tags input
	$('input[name="data[Movement][tags]"]').tagit();
	$(".tagit-new input").attr("placeholder", "New Tag");

	// Update the movement preview when typing
	$('#input-title').keyup(function () { $('#preview-title').html( $('#input-title').val() ); });
	$('#input-description').keyup(function () { $('#preview-description').html( $('#input-description').val() ); });
	$('#input-location').keyup(function () { $('#preview-location').html( $('#input-location').val() ); });

	// Check if photo stored
	var image = document.getElementById("movement-image");
	if (""==image.value) {
		// image not set
	} else {
		$('#files').append('<img src="https://app-movement.com/img/movements/small/'+image.value+'" style="display:inline-block; margin-right: 10px; max-height:80px; max-width:80px;" />');
        $('#preview-image').css('background-image', 'url(https://app-movement.com/img/movements/medium/'+image.value+')');
        $('#progress .progress-bar').css('width', '100%');
	}
	
	$('#type-selector li:nth(' + ($('#app-type-input').val() - 1) + ')').addClass('selected');
	
	// Setup type selector
	$('.type-selector .available-type').click(function() {

		// Alter visual style
		$('.type-selector li').removeClass('selected');
		$(this).addClass('selected');

		var type = $(this).attr('data-index');

		$('#app-type-input').val(type);

		showModal(type, true);

		// Set form value
		$('#app-type-input').val(type);

	});

	// Setup type selector
	$('.type-selector .unavailable-type').click(function() {

		var type = $(this).attr('data-index');

		showModal(type, false);

	});

	// Update

});

function showModal(type, available) {
	
	type--;

	$('#app-type-modal-name').html(app_type_names[type]);
	$('#app-type-modal-description').html(app_type_descriptions[type]);
	$('#app-type-modal-examples').html(app_type_examples[type]);

	if (available) {
		$('#app-type-development').hide();
		$('#app-type-modal-dismiss-button').html(lang.startMovement.confirm);
		$('#app-type-modal-dismiss-button').removeClass('btn-danger').addClass('btn-success');
	} else {
		$('#app-type-development').show();
		$('#app-type-modal-dismiss-button').html(lang.startMovement.cancel);
		$('#app-type-modal-dismiss-button').removeClass('btn-success').addClass('btn-danger');
	}

	// Display info modal
	setTimeout(function() {
		if (type == app_type_names.length) {
			$('#app-type-suggest-modal').modal('show');
		} else {
			$('#app-type-modal').modal('show');
		}
	}, 300);
}