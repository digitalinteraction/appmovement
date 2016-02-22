<!-- About view -->

<?php
echo $this->Html->css('about', null, array('inline'=>false));
?>

<!-- Header -->
<div class="row banner-row">
	<div class="container">
		<h3><?php echo __('How it Works'); ?></h3>
	</div>
</div>

<!-- Steps -->
<div class="row steps-row">
	<div class="container">

		<div class="col-sm-4">
			<div class="step">
				<?php echo $this->Html->image('how-it-works/idea.png', array('class' => 'img-responsive step', 'alt' => 'Start your movement with a great idea')); ?>
				<h4>1. <?php echo __('Start your movement'); ?></h4>
				<p><?php echo __('Add your idea to the site and get your friends and family to support your campaign.'); ?></p>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="step">
				<?php echo $this->Html->image('how-it-works/community.png', array('class' => 'img-responsive step', 'alt' => 'Design with your community')); ?>
				<h4>2. <?php echo __('Design together'); ?></h4>
				<p><?php echo __('Submit ideas and vote on community contributions to select the features you want.'); ?></p>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="step">
				<?php echo $this->Html->image('how-it-works/phones.png', array('class' => 'img-responsive step', 'alt' => 'Share your app with the world')); ?>
				<h4>3. <?php echo __('Launch the app'); ?></h4>
				<p><?php echo __('We build your app based on the results of the design phase and release it for iOS and Android.'); ?></p>
			</div>
		</div>
	</div>
</div>

<!-- Start Colour Bar	 -->
<div class="row color-row start-row">
	<div class="number step-1">
		<span>1</span>
	</div>
	<h1><?php echo __('Start your movement'); ?></h1>
	<h3><?php echo __('Share your app idea with the world and get your community involved'); ?></h3>
</div>

<!-- Start info -->
<div class="container info-stub">
	<div class="row">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/movement-page.png', array('class' => 'img-responsive', 'alt' => 'Start your movement with a great idea')); ?>
				</div>
				<h4><?php echo __('Start your movement'); ?></h4>
				<p><?php echo __('Post your awesome app idea to the site and start your movement. Select from an app template, add a title and description and hit start!'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/community.png', array('class' => 'img-responsive', 'alt' => 'Engage with your community')); ?>
				</div>
				<h4><?php echo __('Engage your Community'); ?></h4>
				<p><?php echo __('Get support from your community and promote your movement through social media to reach your target.'); ?></p>
			</div>
		</div>
	</div>

	<div class="row hidden-xs hidden-sm">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/share-icons.png', array('class' => 'img-responsive', 'alt' => 'Share your idea with friends')); ?>
				</div>
				<h4><?php echo __('Share your idea with friends'); ?></h4>
				<p><?php echo __('Hit your target by sharing your movement through social media. Get your friends involved so they can get in on the action too.'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/target.png', array('class' => 'img-responsive', 'alt' => 'Hit your target')); ?>
				</div>
				<h4><?php echo __('Hit your target'); ?></h4>
				<p><?php echo __('Reach your target number of supporters for yout idea and proceed to designing your app.'); ?></p>
			</div>
		</div>
	</div>
	
</div>

<!-- Design Color Bar -->
<div class="row color-row design-row">
	<div class="number step-2">
		<span>2</span>
	</div>
	<div class="wrap">
		<h1><?php echo __('Design with your community'); ?></h1>
		<h3><?php echo __('Share ideas and vote on the best features you want in the app.'); ?></h3>
	</div>
</div>

<!-- Design Info -->
<div class="container info-stub">
	<div class="row">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/design-phase.png', array('class' => 'img-responsive', 'alt' => 'Discuss your ideas0')); ?>
				</div>
				<h4><?php echo __('Contribute ideas'); ?></h4>
				<p><?php echo __('Got a great idea for the name? Want to pick the colour scheme? Submit your idea to the community.'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/vote.png', array('class' => 'img-responsive', 'alt' => 'Vote on features')); ?>
				</div>
				<h4><?php echo __('Vote on features'); ?></h4>
				<p><?php echo __('Work together to vote on the best ideas from within your community. The best ideas with the most votes are selected for the final app.'); ?></p>
			</div>
		</div>
	</div>

	<div class="row hidden-xs hidden-sm">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/discuss.png', array('class' => 'img-responsive', 'alt' => 'Discuss your ideas')); ?>
				</div>
				<h4><?php echo __('Discuss your ideas'); ?></h4>
				<p><?php echo __('Start the conversation around what features you want in the app and what they might look like.'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/generate-circle.png', array('class' => 'img-responsive', 'alt' => 'Get ready your app is being generated')); ?>
				</div>
				<h4><?php echo __('Your app is generated by us'); ?></h4>
				<p><?php echo __('Once the best ideas have been voted on we\'ll automatically add them to the app.'); ?></p>
			</div>
		</div>
	</div>
	
</div>

<!-- Launch Colour Bar -->
<div class="row color-row launch-row">
	<div class="number step-3">
		<span>3</span>
	</div>
	<div class="wrap">
		<h1><?php echo __('Launch your app to the world'); ?></h1>
		<h3><?php echo __('Your app is automatically generated and free for your community to download.'); ?></h3>
	</div>
</div>

<!-- Launch Info -->
<div class="container info-stub">
	<div class="row">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/phones.png', array('class' => 'img-responsive', 'alt' => 'Download your app')); ?>
				</div>
				<h4><?php echo __('Download your app'); ?></h4>
				<p><?php echo __('We\'ll automatically generate the app for Android and iOS and tell you when it\'s ready so you can be the first to download it!'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/group.png', array('class' => 'img-responsive', 'alt' => 'Share with everyone')); ?>
				</div>
				<h4><?php echo __('Share with everyone'); ?></h4>
				<p><?php echo __('Excited by your new app? Tell people about it and get them to download the app so they can use it too!'); ?></p>
			</div>
		</div>
		
	</div>

	<div class="row hidden-xs hidden-sm">
		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/sustain-your-community.png', array('class' => 'img-responsive', 'alt' => 'Sustain your community')); ?>
				</div>
				<h4><?php echo __('Sustain your community'); ?></h4>
				<p><?php echo __('The app will be available to everyone, it\'s important you contribute content as much as possible to improve it for others.'); ?></p>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="feature-item">
				<div class="img-wrapper">
					<?php echo $this->Html->image('how-it-works/people/man_epic.png', array('class' => 'img-responsive', 'alt' => 'Feel awesome!')); ?>
				</div>
				<h4><?php echo __('Feel awesome!'); ?></h4>
				<p><?php echo __('Congratulations you\'ve made it! You can feel awesome knowing you\'ve released an app for everyone to enjoy, good job!'); ?></p>
			</div>
		</div>
	</div>	
</div>

<div class="start-your-movement-container">
	
	<?php if ($this->Session->read('Auth.User')): ?>

		<?php echo $this->Html->link('<div class="btn btn-success">' . __('Get Started') . '</div>', array('controller' => 'movements', 'action' => 'start'), array('escape' => false, 'id' => 'start-movement-btn')); ?>

	<?php else: ?>

		<?php echo $this->Html->link('<div class="btn btn-success">' . __('Get Started') . '</div>', array('controller' => 'users', 'action' => 'register'), array('escape' => false, 'id' => 'start-movement-btn')); ?>

	<?php endif; ?>

</div>

<script type="text/javascript">

	$('#about-tab li').addClass('active');

</script>