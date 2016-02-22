<style type="text/css">

.movement-update {
	border-top: #e1e1e1 1px dashed;
	padding: 15px 20px;
	position: relative;
}

.movement-update:first-child {
	border-top: none;
	padding-top: 0px;
}

.movement-update .update-created-ago {
	color: #999;
	font-size: 14px;
}

.movement-update .fa-bookmark {
	color: orange;
	margin: 0px 8px;
}

.movement-update .fa-trash-o {
	color: #999;
	font-size: 14px;
}

.movement-update .fa-trash-o:hover {
	cursor: pointer;
	color: #f00;
}

.movement-update h4 {
	color: #000;
	font-size: 15px;
}

.movement-update p {
	color: #000;
	padding-left: 18px;
}

.movement-update-trash {
	position: absolute;
	right: 15px;
	top: 15px;
}

</style>

<div class="movement-update" id="update-<?php echo $movement_update["id"]; ?>">

	<div class="movement-update-trash">
		<?php if ($movement_update["user_id"] == $this->Session->read('Auth.User.id')) { echo '<i class="update-delete-button fa fa-trash-o" style="margin-left: 8px" data-updateid="' . $movement_update["id"] . '"></i>'; } ?>
	</div>

	<h4><i class="fa fa-bookmark"></i><?php echo __('Update shared'); ?> <span class="update-created-ago" title="<?php echo $movement_update["created"]; ?>"></span></h4>
	<p><?php echo nl2br(strip_tags($movement_update["text"], '<br>,<b>,<center>,<a>')); ?></p>

</div>