<?php echo $this->Html->css('dashboard', null, array('inline'=>false)); ?>

<div class="feed-container">

<?php foreach($notifications as $notification): ?>
	<?php echo $this->element('Emails/html/support_phase_complete', array('json_data' => $notification["Notification"]["data"])); ?>	
<?php endforeach; ?>

<?php echo $this->element('Notifications/new_supporters', array('count' => 14)); ?>
<?php echo $this->element('Notifications/new_promoters'); ?>
<?php echo $this->element('Notifications/created_movement'); ?>
</div>