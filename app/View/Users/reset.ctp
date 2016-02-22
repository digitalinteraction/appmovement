<!-- Reset view -->

<?php
echo $this->Html->css('forgot', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Reset Password'); ?></h3>

	</div>
</div>

<div class="row">
	<div class="container form-container">

		<h4><?php echo __('Reset Your Password'); ?></h4>
		<p><?php echo __('Please enter new password below.'); ?></p>

		<hr />
	
		<?php echo $this->Form->create('User', array('class' => 'forgot-form')); ?>
		
			<?php echo $this->Form->hidden('code', array('value' => $code)); ?>

			<?php echo $this->Form->hidden('email', array('value' => $email)); ?>
			
			<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('New Password'))); ?>

			<?php echo $this->Form->input('password_confirm', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('Confirm Password'), 'label' => __('Please re-enter password'))); ?>
			
			<?php
			$options = array(
			    'label' => __('Reset Password'),
			    'class' => 'btn btn-success btn-lg',
			);
			?>

		<?php echo $this->Form->end($options); ?>

	</div>
</div>