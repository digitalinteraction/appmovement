<!-- Analytics web view -->

<?php
echo $this->Html->meta(array('name' => 'og:title', 'content' => $published_app["PublishedApp"]["store_listing_name"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"]), NULL, array('inline' => false));
?>

<?php
echo $this->Html->css('published_apps/map', null, array('inline'=>false));
echo $this->Html->script('published_apps/map', array('inline'=>false));
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

<script>

	var map;
	var markers = [];
	var coordinates = new google.maps.LatLng(54.9783154,-1.6179360);

	function initialize() {
		var mapOptions = {
			zoom: 5,
			center: coordinates,
			disableDefaultUI: true,
			zoomControl: true,
			styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#cad7f9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#ece7d6"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
		};
		map = new google.maps.Map(document.getElementById('map-block'),
	      mapOptions);

		<?php foreach ($reviews as $index => $review) { ?>

			<?php if(isset($review["Venue"]["longitude"]) && isset($review["Venue"]["longitude"])): ?>
			var marker_coordinates = new google.maps.LatLng(<?php echo $review["Venue"]["latitude"]; ?>, <?php echo $review["Venue"]["longitude"]; ?>);

			var marker = new google.maps.Marker({
				position: marker_coordinates
			});

			markers.push(marker);
			<?php endif; ?>
		<?php } ?>

		setAllMap(map);
	}

	google.maps.event.addDomListener(window, 'load', initialize);

	// Sets the map on all markers in the array.
	function setAllMap(map) {
	  for (var i = 0; i < markers.length; i++) {
	    markers[i].setMap(map);
	  }
	}
</script>

<div class="block" id="map-block"></div>