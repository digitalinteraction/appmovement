<!-- Site users view -->

<div class="row banner-row">
	<div class="container">
		<h3><?php echo __('Site users'); ?></h3>
	</div>
</div>

<style type="text/css">

	table {
		border: #e6e6e6 1px solid;
		border-radius: 4px;
		width: 100%;
	}

	table thead {
		background-color: #f2f2f2;
	}

</style>

<div class="row">

	<div class="container">

		<div class="page-panel">

			<table border="1" cellpadding="5px">

				<thead>
					<td>#</td>
					<td><?php echo __('Photo'); ?></td>
					<td><?php echo __('Fullname'); ?></td>
					<td><?php echo __('Username'); ?></td>
					<td><?php echo __('Email'); ?></td>
					<td><?php echo __('Joined'); ?></td>
				</thead>

				<?php foreach ($users as $index => $user) { ?>
					
					<tr>
						<td><?php echo $user["User"]["id"]; ?></td>
						<td><?php echo $this->Html->image('users/thumb/' . $user["User"]["photo"]); ?></td>
						<td><?php echo $user["User"]["fullname"]; ?></td>
						<td><?php echo $user["User"]["username"]; ?></td>
						<td><?php echo $user["User"]["email"]; ?></td>
						<td><?php echo date("d-m-Y", strtotime($user["User"]["created"])); ?></td>
					</tr>

				<?php } ?>

			</table>

		</div>

	</div>

</div>