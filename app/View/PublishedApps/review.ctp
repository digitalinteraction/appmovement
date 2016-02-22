<!-- Review web view -->

<?php
echo $this->Html->meta(array('name' => 'og:title', 'content' => $published_app["PublishedApp"]["store_listing_name"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"]), NULL, array('inline' => false));
?>

<?php
echo $this->Html->css('published_apps/review', null, array('inline'=>false));
echo $this->Html->script('published_apps/review', array('inline'=>false));
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

<script>

	var map;
	var coordinates = new google.maps.LatLng(<?php echo $venue["Venue"]["latitude"]; ?>, <?php echo $venue["Venue"]["longitude"]; ?>);

	function initialize() {
		var mapOptions = {
			zoom: 12,
			center: coordinates,
			disableDefaultUI: true,
			zoomControl: true,
			styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#cad7f9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#ece7d6"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]
		};
		map = new google.maps.Map(document.getElementById('map-block'),
	      mapOptions);

		var marker = new google.maps.Marker({
			position: coordinates,
			map: map,
			title: '<?php echo $venue["Venue"]["name"]; ?>'
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div class="row">

	<div class="container">

		<div class="venue-name">
			<h2>
				<?php echo $venue["Venue"]["name"]; ?>
			</h2>
			<?php echo $this->Element('star_rating', array('rating' => $venue["Venue"]["average_review"])); ?>
		</div>

		<div class="venue-container">

			<?php
			echo $this->Html->link('<i class="fa fa-chevron-left"></i>', array('controller' => 'published_apps', 'action' => 'review', $published_app["PublishedApp"]["id"], $neighbours["prev"]["Review"]["id"]), array('escape' => false,'class' => 'navigation-arrow', 'id' => 'prev-arrow'));
			echo $this->Html->link('<i class="fa fa-chevron-right"></i>', array('controller' => 'published_apps', 'action' => 'review', $published_app["PublishedApp"]["id"], $neighbours["next"]["Review"]["id"]), array('escape' => false,'class' => 'navigation-arrow', 'id' => 'next-arrow'));
			?>

			<div class="block" id="review-block">

				<div class="user-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $user['User']['photo']; ?>)"></div>

				<div class="review-text">
					<h4 class="user-fullname"><?php echo $user["User"]["fullname"]; ?></h4>
					<?php echo $review["Review"]["review_text"]; ?>	
					<?php $rating = ($review["Review"]["q1"] + $review["Review"]["q2"] + $review["Review"]["q3"] + $review["Review"]["q4"]) / 4; ?>
					<?php echo $this->Element('star_rating', array('rating' => $rating)); ?>
				</div>

			</div>

			<div class="block" id="map-block">
			</div>

			<div class="block" id="photos-block">
				<?php foreach ($review["Photo"] as $index => $photo) { ?>
					<div class="photo-thumbnail" style="background-image: url(http://cdn.app-movement.com/apps/geolocation/uploads/large/<?php echo $photo["filename"]; ?>)"></div>
				<?php } ?>
			</div>

			<?php if (count($venue["Review"]) > 1) { ?>

				<div class="block" id="other-reviews-block">

					<h4><?php echo (count($venue["Review"]) - 1); ?> Other Reviews</h4>

					<?php
					foreach ($venue["Review"] as $index => $other_review) {
					if ($other_review["id"] != $review["Review"]["id"]) { 
					?>
						<div class="other-review">
							<?php $rating = ($other_review["q1"] + $other_review["q2"] + $other_review["q3"] + $other_review["q4"]) / 4; ?>
							<?php echo $this->Element('star_rating', array('rating' => $rating)); ?>
							<span class="review-text">
								<?php echo $other_review["review_text"]; ?>
							</span>
							<span class="review-user">
								- <?php echo $other_review["User"]["fullname"]; ?>
							</span>
						</div>
					<?php
					}
					}
					?>

				</div>

			<?php } ?>
			
		</div>

		<div id="app-download-block">
			<a href="<?php echo $published_app["PublishedApp"]["ios_download_link"]; ?>"><img class="app-download-link" alt="" src="<?php echo $this->webroot . 'img/apple-store.jpg'; ?>"/></a>
			<a href="<?php echo $published_app["PublishedApp"]["android_download_link"]; ?>"><img class="app-download-link" alt="" src="<?php echo $this->webroot . 'img/android-store.jpg'; ?>" /></a>
		</div>

	</div>

</div>