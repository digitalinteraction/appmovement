
<!-- Location Task Element -->

<?php
echo $this->Html->css('tasks/location', null, array('inline'=>false));
?>

<div class="row">
	<div class="container">
		<div class="contributions-container" style="margin-top: 0px; text-align:center">
			<div id="contributions_wrapper" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 270, "gutter": 20, "itemSelector": ".tile" }'>
				<?php
				foreach ($contributions as $item) {
					echo $this->element('Contributions/' . $design_task["AppTypeDesignTask"]["DesignTask"]["element"], array('item' => $item));
				} 
				?>
			</div>
			<div class="clearfloat"></div>
	
		<div class="map-container">
			<div id="map-canvas"></div>
			<div id="map-overlay"><i class="fa fa-location-arrow" style="margin-right:10px"></i>User's Location</div>
		</div>

		<br />
			<div class="contribute-container">
			<h2>Submit <?php echo $design_task["AppTypeDesignTask"]["DesignTask"]["name"]; ?></h2>
			<div class="alert alert-warning" id="contribution-form-errors">
				<ul id="contribution-form-errors-list">
				</ul>
			</div>
			<?php echo $this->Form->create('Contribution', array('type' => 'post', 'action' => '/add', 'id' => 'contributon-add-form')); ?>
			<div class="map-controls">
				<div>
					<ul>
						<li>Click "<strong>User's Location</strong>" if you would like the app to <strong>open the map at the user's current location</strong>.</li>
						<li>Click "<strong>Drop Pin</strong>" to place and drag a pin on the map where you would like the app to <strong>open the map at that specific location</strong>.</li>
					</ul>
				</div>
				<br />
				<div id="selected_user_location">
				</div>
				<button id="locationButton" class="btn btn-lg btn-info"><i class="fa fa-location-arrow" style="margin-right:5px"></i> User's Location</button>

				<button id="pinButton" class="btn btn-lg btn-info"><i class="fa fa-map-marker" style="margin-right:5px"></i> Drop Pin</button>

			</div>
	    	<?php echo $this->Form->input('contribution_type_id', array('class' => 'input-field', 'id' => 'contribution_type_id', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["contribution_type_id"])); ?>
	    	<?php echo $this->Form->input('movement_design_task_id', array('class' => 'input-field', 'id' => 'movement_design_task_id', 'type' => 'hidden', 'value' => $design_task["MovementDesignTask"]["id"])); ?>
	    	<?php echo $this->Form->input('app_type_design_task_element', array('class' => 'input-field', 'id' => 'app_type_design_task_element', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["element"])); ?>

			<br />
			<button class="btn btn-lg btn-success" id="contribution-add-submit">Submit Idea</button>
			<?php echo $this->Form->end(); ?>
		</div>



		<!-- Map Modal -->
		<div class="modal fade" id="contribution-map-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<div id="modal-map-canvas" style="min-height:250px"></div>
		            </div>
					<div class="modal-footer">
						<div class="btn btn-danger btn-sm" data-dismiss="modal">Close</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBETo8cdIvxm6DntdLZZ6w5I5fqL04S5kk&sensor=true"></script>

		<script type="text/javascript">

			var map;
			var modal_map;
			var markerAdded = false;
			var userMarker;
			var user_location = true;
			var contribution_marker_locations = [];
			var geocoder;

			$('#contribution-map-modal').on('shown.bs.modal', function () {
			    google.maps.event.trigger(modal_map, 'resize');
			    modal_map.setZoom(14);
			    modal_map.panTo(new google.maps.LatLng(contribution_marker_locations[0].getPosition().lat(), contribution_marker_locations[0].getPosition().lng()));
			});

			$(document).ready(function () {

				userLocation();
					
				$('#pinButton').bind( "click", function() {
					addMarker();
					$('#selected_user_location').show();
					user_location = false;

					return false;
				});

				$('#locationButton').bind( "click", function() {
					userLocation();
					user_location = true;
					return false;
				});

				$('#contribution-map-modal').modal('hide');
			});

			function geocode_position(lat, lng, callback)
			{
				return geocoder.geocode({'latLng': new google.maps.LatLng(lat, lng)}, function(results, status) {
			      if (status == google.maps.GeocoderStatus.OK) {
			        if (results[2]) {
			        	callback(results[2].formatted_address);
			        }
			        else
			        {
			        	callback("Not found");
			        }
			      } else {
			        console.log(status);
			        callback("Not found");
			      }
		    	});
			}

			function geocode_location_contributions()
			{
				$('.view_on_map').each(function(index){
					geocoder.geocode({'latLng': new google.maps.LatLng($(this).attr("lat"), $(this).attr("lng"))}, function(results, status) {
				      if (status == google.maps.GeocoderStatus.OK) {
				      	console.log(results);
				        if (results[2]) {
				        	console.log(results);
				        }
				      } else {
				        alert("Geocoder failed due to: " + status);
				      }
			    	});
				});
			}

			function clear_map()
			{
				for (var i = 0; i < contribution_marker_locations.length; i++) {
			    	contribution_marker_locations[i].setMap(null);
				}

				contribution_marker_locations = [];
			}

			function addMarkerAtPosition(lat, lng, map_to_add_to)
			{	

				userMarker = new google.maps.Marker({
					    position: new google.maps.LatLng(lat,lng),
					    draggable: false,
					    animation: google.maps.Animation.DROP
					});

				userMarker.setMap(map_to_add_to);
				contribution_marker_locations.push(userMarker);
			}

			function addMarker() {

				$('#pinButton').css('background-color', '#2CD7D7');
				$('#locationButton').css('background-color', '#CCC');
				$('#map-overlay').hide();

				var myLatlng = map.getCenter();
				var image = '<?php echo $this->webroot . 'img/map/user-pin.png'; ?>';

				if (markerAdded == false) {

					markerAdded = true;

					// To add the marker to the map, use the 'map' property
					userMarker = new google.maps.Marker({
					    position: myLatlng,
					    draggable:true,
					    animation: google.maps.Animation.DROP,
						icon: image
					});

					userMarker.setMap(map);

					$('#selected_user_location').show();
					display_user_marker_position(myLatlng.lat(), myLatlng.lng());

					google.maps.event.addListener(userMarker, 'dragend', function(evt){
				    	display_user_marker_position(evt.latLng.lat(), evt.latLng.lng());
				    });

				} else {

				    userMarker.setPosition( myLatlng );
				    map.panTo( myLatlng );

				}

			}

			function display_user_marker_position(lat, lng)
			{
				$('#selected_user_location').show();
				geocode_position(lat, lng, function(place){
			    		console.log(place);
			    		if(place)
			    		{
			    			if(place != "Not found")
			    			{
			    				$('#selected_user_location').html('<p><strong>You have selected</strong></p><h2>' + place + '</h2><p>As the location you want the map to be centered on.</p><p>Drag the marker to change your selection</p>');
			    			}
			    		}
		    	});
			}
		
			function initialize() {

				geocoder = new google.maps.Geocoder();
				// geocode_location_contributions();

				var mapOptions = {
					center: new google.maps.LatLng(-34.397, 150.644),
					zoom: 1
				};

				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				modal_map = new google.maps.Map(document.getElementById('modal-map-canvas'), mapOptions);


				if (navigator.geolocation) {
				     navigator.geolocation.getCurrentPosition(function (position) {
				         initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				         map.setCenter(initialLocation);
				         map.setZoom(8);
				     });
				 }
			
				// // Add markers to map
				// var markers = <?php echo json_encode($contributions); ?>

				// markers.forEach(function(marker) {

				// 	var coordinates = JSON.parse(marker.Contribution.data);

				// 	var myLatlng = new google.maps.LatLng(coordinates.latitude, coordinates.longitude);

				// 	var image = '<?php echo $this->webroot . 'img/map/default-pin.png'; ?>';

				// 	var marker = new google.maps.Marker({
				// 	    position: myLatlng,
				// 	    title:"User Added Location",
				// 		icon: image
				// 	});
					
				// 	marker.setMap(map);
				
				// });
				
			}

			function userLocation() {
				$('#pinButton').css('background-color', '#CCC');
				$('#locationButton').css('background-color', '#2CD7D7');
				$('#map-overlay').show();
				$('#selected_user_location').hide();
			}

			// Init map
			google.maps.event.addDomListener(window, 'load', initialize);

		</script>

	</div>
</div>
