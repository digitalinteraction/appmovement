<!-- Edit view -->

<?php
echo $this->Html->css('edit', null, array('inline'=>true));
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Edit Profile'); ?></h3>

	</div>
</div>

<div class="row">
	<div class="container form-container">

		<?php echo $this->Form->create('User', array('class' => 'edit-form', 'novalidate', 'type' => 'file')); ?>

			<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $user['User']['photo']; ?>)"></div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('fullname', array('class' => 'input-field', 'placeholder' => __('Your Name'), 'value' => $user["User"]["fullname"])); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('username', array('class' => 'input-field', 'placeholder' => __('Username'), 'value' => $user["User"]["username"])); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'input-field', 'placeholder' => __('Email Address'), 'value' => $user["User"]["email"])); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('Password'))); ?>
			</div>

			<hr />

			<div class="form-group input-section file-upload">
		    	<?php echo $this->Form->input('photo',array('type'=>'file', 'label' => __('Profile Photo'))); ?>
			</div>

			<hr />

			<div class="form-group input-section">
				<label><strong><?php echo __('Notification Preferences'); ?></strong></label>

				<div class="checkbox">
				  <label>
				    <?php echo $this->Form->input('receives_email_updates', array('type' => 'checkbox', 'label' => false, 'checked' => $user["User"]["receives_email_updates"])); ?>
				    <?php echo __('Send me awesome email updates'); ?>
				  </label>
				</div>

			</div>

			<hr />

			<br />

			<?php
			$options = array(
			    'label' => __('Update Profile'),
			    'class' => 'btn btn-success btn-lg',
			);
			?>

		<?php echo $this->Form->end($options); ?>

	</div>
</div>