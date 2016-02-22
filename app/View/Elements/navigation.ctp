<!-- Navigation -->

<?php echo $this->Html->css('navigation', null, array('inline'=>true)); ?>

<div class="row" id="navigation">
	<div class="container">

		<div id="logo">
			<?php echo $this->Html->link('<strong>APP</strong>MOVEMENT', array('controller' => 'pages', 'action' => 'display', 'home'), array('escape' => false)); ?>
		</div>

		<ul>
			<?php
			if ($this->Session->read('Auth.User')) {
				
				echo $this->Html->link('<li>' . __('About') . '</li>', array('controller' => 'pages', 'action' => 'display', 'about'), array('escape' => false, 'id' => 'about-tab'));
				echo $this->Html->link('<li>' . __('Start') . '</li>', array('controller' => 'movements', 'action' => 'start'), array('escape' => false, 'id' => 'start-tab'));
				echo $this->Html->link('<li>' . __('Discover') . '</li>', array('controller' => 'movements', 'action' => 'index'), array('escape' => false, 'id' => 'discover-tab'));
				echo $this->Html->link('<li>' . __('Activity') . '</li>', array('controller' => 'movements', 'action' => 'overview'), array('escape' => false, 'id' => 'activity-tab'));
				echo $this->Html->link('<li><div id="user-profile">' . $this->Html->image('users/thumb/' . $this->Session->read('Auth.User.photo')) . '</div></li>', array('controller' => 'users', 'action' => 'dashboard'), array('escape' => false, 'id' => 'dashboard-tab'));
			} else {
				echo $this->Html->link('<li>' . __('About') . '</li>', array('controller' => 'pages', 'action' => 'display', 'about'), array('escape' => false, 'id' => 'about-tab'));
				echo $this->Html->link('<li>' . __('Start') . '</li>', array('controller' => 'movements', 'action' => 'start'), array('escape' => false, 'id' => 'start-tab'));
				echo $this->Html->link('<li>' . __('Discover') . '</li>', array('controller' => 'movements', 'action' => 'index'), array('escape' => false, 'id' => 'discover-tab'));
				echo $this->Html->link('<li>' . __('Login') . '</li>', array('controller' => 'users', 'action' => 'login'), array('escape' => false, 'id' => 'login-tab'));
				echo $this->Html->link('<li>' . __('Register') . '</li>', array('controller' => 'users', 'action' => 'register'), array('escape' => false, 'id' => 'register-tab'));
				echo $this->Html->link('<li class="nav-button"><div class="btn btn-success">' . __('Create Account') . '</div></li>', array('controller' => 'users', 'action' => 'register'), array('escape' => false, 'id' => 'register-btn'));
			}
			?>
		</ul>

	</div>
</div>