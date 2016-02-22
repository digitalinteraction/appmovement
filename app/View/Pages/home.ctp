<!-- Home view -->

<?php
echo $this->Html->css('home', null, array('inline'=>false));
?>

<div class="row hero-row">
	<div class="container">

		<div class="col-md-7">
			<div class="hero-content">
				<h4><?php echo __('Your Community. Your App.'); ?></h4>

				<p><?php echo __('App Movement enables anyone to propose, design and develop a mobile application. No expertise are required and the process is fun and simple. Watch the video below to get started.'); ?></p>
				
				<?php
				if (strtolower($this->Session->read('Config.language')) == "ar") {
					
					echo $this->Html->link( '<div class="btn btn-info btn-lg"><i class="fa fa-play-circle"></i> ' . __('Watch the Video') . '</div>', 'https://www.youtube.com/watch?v=mOKNHTWhtJQ', array('class' => 'fancybox-media', 'escape' => false) );
				} else {
					echo $this->Html->link( '<div class="btn btn-info btn-lg"><i class="fa fa-play-circle"></i> ' . __('Watch the Video') . '</div>', 'https://www.youtube.com/watch?v=F6wOuj-6CB0', array('class' => 'fancybox-media', 'escape' => false) );
				}
				?>
			</div>
		</div>

	</div>
</div>

<div class="row process-row">
	<div class="container">

		<div class="col-sm-4">
			<div class="process-content">
				<?php echo $this->Html->image('how-it-works/idea.png', array('class' => 'img-responsive', 'alt' => 'Start your movement with a great idea')); ?>
				<h5><?php echo __('Start a Movement'); ?></h5>
				<p><?php echo __('Choose one of the app templates and start your movement. Gain support for your movement by sharing it with friends.'); ?></p>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="process-content">
				<?php echo $this->Html->image('how-it-works/community.png', array('class' => 'img-responsive', 'alt' => 'Design with your community')); ?>
				<h5><?php echo __('Design Together'); ?></h5>
				<p><?php echo __('The supporters of your movement work together to design the app by submitting and voting on ideas.'); ?></p>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="process-content">
				<?php echo $this->Html->image('how-it-works/phones.png', array('class' => 'img-responsive', 'alt' => 'Share your app with the world')); ?>
				<h5><?php echo __('Generate Application'); ?></h5>
				<p><?php echo __('You app is automatically generated based on the results of the design process and it is released in the app store.'); ?></p>
			</div>
		</div>

	</div>
</div>

<div class="row featured-row">
	<div class="container">

		<h2><?php echo __('Featured Movements'); ?></h2>

		<?php if (count($movements) == 0) {
			echo '<div class="no-results"><h3>' . __('No Movements Found') . '</h3></div>';
		} ?>

		<div id="movements-container" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 265, "gutter": 20, "itemSelector": ".movement-tile" }'>

			<?php foreach ($movements as $index => $movement) {

				echo $this->element('movement_tile', array('movement' => $movement));

			} ?>

		</div>
		
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {

	var height = screen.height/2;

	$('.fancybox-media').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',
		helpers : {
			media : {}
		},
       'width' : 16/9. * height,
       'height' : height,
       'autoDimensions' : false
	});

});

</script>
