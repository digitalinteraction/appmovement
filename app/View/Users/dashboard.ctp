<!-- Dashboard view -->

<?php
echo $this->Html->css('dashboard', null, array('inline'=>false));
echo $this->Html->css('notifications', null, array('inline'=>false));

echo $this->Html->script('dashboard', array('inline'=>false));
echo $this->Html->script('notifications');
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Your Dashboard'); ?></h3>

	</div>
</div>

<div class="row profile-row">
	<div class="container">

		<div class="col-md-3">

			<div class="profile-container">

				<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $user['User']['photo']; ?>)"></div>

				<h2><?php echo $user['User']['fullname']; ?></h2>

				<?php echo '<h5>' . __('Joined') . ' ' . date("Y", strtotime($user['User']['created'])) . '</h5>'; ?>

				<?php 
				if ($user['User']['role'] === 'admin')
				{
					echo $this->Html->link( '<div class="btn btn-info btn-block">' . __('Admin Section') . '</div>', array('controller' => 'users', 'action' => 'admin'), array('escape' => false) );
					echo $this->Html->link( '<div class="btn btn-info btn-block">' . __('Site Users') . '</div>', array('controller' => 'users', 'action' => 'display_users'), array('escape' => false) );
				}
				?>

				<?php echo $this->Html->link( '<div class="btn btn-warning btn-block">' . __('Edit Profile') . '</div>', array('controller' => 'users', 'action' => 'edit'), array('escape' => false) ); ?>
				<?php echo $this->Html->link( '<div class="btn btn-danger btn-block">' . __('Logout') . '</div>', array('controller' => 'users', 'action' => 'logout'), array('escape' => false) ); ?>
			</div>

		</div>

		<div class="col-md-9 feed-column">

			<?php if (count($movements) > 0) { ?>

				<div class="movements-container">

					<div class="section-header">
						<?php echo __('Your Movements'); ?>
					</div>

					<div class="app-container">

						<div class="app-tile-wrapper">

							<?php foreach ($movements as $movement) { ?>

								<div class="app-tile" onClick="document.location = '<?php echo $this->webroot . 'view/' . $movement["Movement"]["id"]; ?>'" style="background-image:url('<?php echo $this->webroot . 'img/movements/medium/' . $movement["MovementPhoto"][0]["filename"]; ?>')">

								</div>

							<?php } ?>

						</div>

					</div>

				</div>

			<?php } ?>

			<div class="feed-container">

				<div class="section-header">
					<?php echo __('Your Notification Stream'); ?>
				</div>

				<!-- loop through feed and output the elements -->

				<?php 
				foreach($notifications as $notification)
				{
					echo $this->element('Notifications/' . $notification["NotificationType"]["view_name"], array('notification' => $notification)); 
				} 
				?>

				<?php echo $this->element('Notifications/welcome'); ?>

			</div>

		</div>

	</div>
</div>


<script type="text/javascript">

$(document).ready(function () {

	addHandlers();

});

// Show share movement window
function shareMovement(windowUri)
{
	var centerWidth = (window.screen.width - 600) / 2;
    var centerHeight = (window.screen.height - 440) / 2;

    newWindow = window.open(windowUri, 'Share Movement', 'resizable=1,width=' + 600 + ',height=' + 440 + ',left=' + centerWidth + ',top=' + centerHeight);
    newWindow.focus();
    return newWindow.name;
}

function addHandlers() {

	$('.share-button').click(function(){
		var movement_id = $(this).attr('data-movement');
		console.log(movement_id);
		var data_start = $(this).attr('data-start');
		var data_end = $(this).attr('data-end');
	});

}

</script>

<script type="text/javascript">

	$('#dashboard-tab li').addClass('active');

</script>