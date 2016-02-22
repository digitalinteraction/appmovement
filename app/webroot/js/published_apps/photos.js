// Published App Photos Script

$(document).ready(function() {

	setTileSize();

	$(window).resize(function() {

		setTileSize();

	});

});

function setTileSize() {

	var dimension = $('body').width() / 6;

	$('.photo-tile').css('width', dimension + 'px');

	$('.photo-tile').css('padding-bottom', dimension + 'px');

	$('.photo-tile').click(function() {

		var filename = $(this).attr('data-filename');

		$(this).css('backgroundImage','url(\'http://cdn.app-movement.com/apps/geolocation/uploads/large/' + filename + '\')')

	});
}