<?php
$json = $item["Contribution"]["data"];
$contribution = json_decode($json);
?>

<div class="tile name-tile" id="contribution-<?php echo $item["Contribution"]["id"]; ?>">

	<?php if (($item["Contribution"]["user_id"] == $this->Session->read('Auth.User.id')) || ($movement["Movement"]["phase"] != 1) || !$is_supporter) { ?>

		<table>
			<tr>
				<td valign="middle" rowspan="3" style="width:100%"><h3><?php echo $contribution->name; ?></h3></td>
				
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
				<td valign="middle" rowspan="3" style="width:100%"><h3><?php echo $contribution->name; ?></h3></td>

				<td class="up-vote-button" valign="middle" data-contribution="<?php echo $item["Contribution"]["id"]; ?>">
					<i class="fa fa-3x fa-caret-up" id="vote-up-<?php echo $item["Contribution"]["id"]; ?>" <?php if($item["Contribution"]["user_vote"] == 1) { echo 'style="color:#5BCA5B"'; } ?>></i>
				</td>
				<i class="fa fa-flag-o contribution-flag" data-contributionid="<?php echo $item["Contribution"]["id"]; ?>"></i>
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
