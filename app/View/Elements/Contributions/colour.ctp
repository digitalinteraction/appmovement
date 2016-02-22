<?php
$json = $item["Contribution"]["data"];
$contribution = json_decode($json);
?>

<div class="tile colour-tile" id="contribution-<?php echo $item["Contribution"]["id"]; ?>">

	<?php if (($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) || ($movement["Movement"]["phase"] != 1) || !$is_supporter) { ?>

		<?php if ($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) { echo '<div class="contributed-banner"></div>'; } ?>
		
		<table>
			<tr>
				<td valign="middle" rowspan="3">
					<div class="colour-swatch" style="background-color:<?php echo $contribution->primary; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo __('Primary Colour'); ?>"></div>
					<div class="colour-swatch" style="background-color:<?php echo $contribution->pin; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo __('Pin Colour'); ?>"></div>
					<div class="colour-swatch" style="background-color:<?php echo $contribution->star; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo __('Star Colour'); ?>"></div>
					<a class="preview-button" data-contribution="<?php echo $item["Contribution"]["id"]; ?>" data-primary-colour="<?php echo $contribution->primary; ?>" data-pin-colour="<?php echo $contribution->pin; ?>" data-star-colour="<?php echo $contribution->star; ?>" data-toggle="modal" data-target="#preview-modal"><?php echo __('Preview'); ?></a>
				</td>
				
				<td class="padding" valign="middle"></td>
			</tr>
			<tr>
				<td class="vote-count" id="vote-count-<?php echo $item["Contribution"]["id"]; ?>" valign="middle">
					<?php echo ($item["Contribution"]["up_votes"] - $item["Contribution"]["down_votes"]); ?>
				</td>
			</tr>
			<tr>
				<?php if ($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) { ?>
					<td class="padding" valign="middle">
						<i class="fa fa-trash-o contribution-delete-button" data-contributionid="<?php echo $item["Contribution"]["id"]; ?>"></i>
					</td>
				<?php } else { ?>
					<td class="padding" valign="middle"></td>
				<?php } ?>
			</tr>
		</table>

	<?php } else { ?>

		<table>
			<tr>
				<td valign="middle" rowspan="3">
					<div class="colour-swatch" style="background-color:<?php echo $contribution->primary; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo __('Primary Colour'); ?>"></div>
					<div class="colour-swatch" style="background-color:<?php echo $contribution->pin; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo __('Pin Colour'); ?>"></div>
					<div class="colour-swatch" style="background-color:<?php echo $contribution->star; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo __('Star Colour'); ?>"></div>
					<a class="preview-button" data-contribution="<?php echo $item["Contribution"]["id"]; ?>" data-primary-colour="<?php echo $contribution->primary; ?>" data-pin-colour="<?php echo $contribution->pin; ?>" data-star-colour="<?php echo $contribution->star; ?>" data-toggle="modal" data-target="#preview-modal"><?php echo __('Preview'); ?></a>
				</td>

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

	$(document).ready(function() {

		$('.colour-swatch').tooltip('show').tooltip('hide');

		$('.preview-button').unbind().click(function() {

	        var contribution_id = $(this).attr("data-contribution");
	        var primary_colour = $(this).attr("data-primary-colour").replace('#', '');
	        var pin_colour = $(this).attr("data-pin-colour").replace('#', '');
	        var star_colour = $(this).attr("data-star-colour").replace('#', '');

	        $('#preview-modal #iframe-venue-preview').attr('src', base_url + '/preview/geolocation_venue/?primary_colour=' + primary_colour + '&pin_colour=' + pin_colour + '&star_colour=' + star_colour);

		});

	});

</script>
