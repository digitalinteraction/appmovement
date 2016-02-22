<?php
$json = $item["Contribution"]["data"];
$contribution = json_decode($json);
?>

<div class="tile location-tile">

	<?php if (($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) || ($movement["Movement"]["phase"] != 1)) { ?>

		<?php if ($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) { echo '<div class="contributed-banner"></div>'; } ?>

		<table>
			<tr>
				<?php if($contribution->lat == -1 && $contribution->lng == -1): ?>

					<td valign="middle" rowspan="3" style="width:100%">
						<?php echo $this->Html->image('map/map.jpg', array('class' => 'map-thumbnail')); ?>
						<p><small>Open at User's Location</small></p>
					</td>

				<?php else: ?>

					<td valign="middle" rowspan="3" style="width:100%"  class="view_on_map" lat="<?php echo $contribution->lat;?>" lng="<?php echo $contribution->lng;?>">
						<img class="map-thumbnail" src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $contribution->lat;?>,<?php echo $contribution->lng;?>&zoom=16&size=180x100&markers=color:0x33CCCC|<?php echo $contribution->lat;?>,<?php echo $contribution->lng;?>&key=AIzaSyDafTQNVnFStC7FX6rtyidAW902aauBpKM&sensor=false" />
						<p class="address"><small><?php echo $contribution->address; ?></small></p>
					</td>

				<?php endif; ?>
				
				<td class="padding" valign="middle"></td>
			</tr>
			<tr>
				<td class="vote-count" id="vote-count-<?php echo $item["Contribution"]["id"]; ?>" valign="middle">
					<?php echo ($item["Contribution"]["up_votes"] - $item["Contribution"]["down_votes"]); ?>
				</td>
			</tr>
			<tr>
				<td class="padding" valign="middle"></td>
			</tr>
		</table>

	<?php } else { ?>

		<table>
			<tr>							
				<?php if($contribution->lat == -1 && $contribution->lng == -1): ?>

					<td valign="middle" rowspan="3" style="width:100%">
						<?php echo $this->Html->image('map/map.jpg', array('class' => 'map-thumbnail')); ?>
						<p><small><?php echo __('Open at User\'s Location'); ?></small></p>
					</td>
				
				<?php else: ?>
				
					<td valign="middle" rowspan="3" style="width:100%"  class="view_on_map" lat="<?php echo $contribution->lat;?>" lng="<?php echo $contribution->lng;?>">
						<img class="map-thumbnail" src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $contribution->lat;?>,<?php echo $contribution->lng;?>&zoom=16&size=180x100&markers=color:0x33CCCC|<?php echo $contribution->lat;?>,<?php echo $contribution->lng;?>&key=AIzaSyDafTQNVnFStC7FX6rtyidAW902aauBpKM&sensor=false" />
						<p class="address"><small><?php echo $contribution->address; ?></small></p>
					</td>

				<?php endif; ?>

				<td class="up-vote-button" valign="middle" data-contribution="<?php echo $item["Contribution"]["id"]; ?>">
					<i class="fa fa-3x fa-caret-up" id="vote-up-<?php echo $item["Contribution"]["id"]; ?>" <?php if($item["Contribution"]["user_vote"] == 1) { echo 'style="color:#5BCA5B"'; } ?>></i>
				</td>
			</tr>
			<tr>
				<td class="vote-count" id="vote-count-<?php echo $item["Contribution"]["id"]; ?>" valign="middle">
					<?php echo ($item["Contribution"]["up_votes"] - $item["Contribution"]["down_votes"]); ?>
				</td>
			</tr>
			<tr>
				<td class="down-vote-button" valign="middle" data-contribution="<?php echo $item["Contribution"]["id"]; ?>">
					<i class="fa fa-3x fa-caret-down" id="vote-down-<?php echo $item["Contribution"]["id"]; ?>" <?php if($item["Contribution"]["user_vote"] == -1) { echo 'style="color:#FF0000"'; } ?>></i>
				</td>
			</tr>
		</table>

	<?php }	?>

</div>


<script type="text/javascript">

function add_map_handler()
{
	$('.view_on_map').on("click", function () {
		clear_map();
		addMarkerAtPosition($(this).attr("lat"), $(this).attr("lng"), modal_map);
		$('#contribution-map-modal').modal('show');
	});	
}

</script>