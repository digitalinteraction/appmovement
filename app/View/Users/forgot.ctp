<!-- Forgot view -->

<?php
echo $this->Html->css('forgot', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Forgot Password'); ?></h3>

	</div>
</div>

<div class="row">
	<div class="container form-container">

		<h4><?php echo __('Send Reset Email'); ?></h4>
		<p><?php echo __('Please enter the email address you used to register to reset your password.'); ?></p>

		<hr />
		
		<?php echo $this->Form->create('User', array('class' => 'forgot-form')); ?>
		
			<?php echo $this->Form->hidden('app_name', array('value' => 'App Movement')); ?>

			<?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'input-field', 'placeholder' => __('Email'))); ?>
			
			<?php
			$options = array(
			    'label' => __('Send Email'),
			    'class' => 'btn btn-info btn-lg',
			);
			?>

		<?php echo $this->Form->end($options); ?>

	</div>
</div>