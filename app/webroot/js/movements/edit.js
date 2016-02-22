/* Edit Movement */

$(document).ready(function() {
	
	// Setup tags input
	$('input[name="data[Movement][tags]"]').tagit();
	$(".tagit-new input").attr("placeholder", "New Tag");

	// Update the movement preview on load
	$('#preview-title').html( $('#input-title').val() );
	$('#preview-description').html( $('#input-description').val() );
	$('#preview-location').html( $('#input-location').val() );

	// Check if photo stored
	var image = document.getElementById("movement-image");
	if (""==image.value) {
		// image not set
	} else {
		$('#files').append('<img src="https://app-movement.com/img/movements/small/'+image.value+'" style="display:inline-block; margin-right: 10px; max-height:80px; max-width:80px;" />');
        $('#preview-image').css('background-image', 'url(https://app-movement.com/img/movements/medium/'+image.value+')');
        $('#progress .progress-bar').css('width', '100%');
	}

	// Update the movement preview when typing
	$('#input-title').keyup(function () { $('#preview-title').html( $('#input-title').val() ); });
	$('#input-description').keyup(function () { $('#preview-description').html( $('#input-description').val() ); });
	$('#input-location').keyup(function () { $('#preview-location').html( $('#input-location').val() ); });

});