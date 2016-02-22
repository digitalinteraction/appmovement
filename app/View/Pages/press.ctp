<!-- Press view -->

<?php
echo $this->Html->css('press', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">
		<h3><?php echo __('Press Kit'); ?></h3>
	</div>
</div>

<div class="row press-row">

	<div class="container">

		<div class="col-md-7 press-col">

			<h2><?php echo __('Press Releases'); ?></h2>

			<div class="press-section press-releases-section">
				
				<p><?php echo __('We welcome you to report on App Movement through your media channels and encourage the use of the materials provided on this page.'); ?></p>

				<ul>
					<li><?php echo $this->Html->link('Newcastle University Press Release <i class="fa fa-external-link"></i>', 'http://www.ncl.ac.uk/press.office/press.release/item/app-movement-why-programming-is-no-longer-the-domain-of-the-computer-geek', array('escape' => false, 'target' => '_blank')); ?></li>
					<li><?php echo $this->Html->link('Newcastle University Dementia Friendly Press Release <i class="fa fa-external-link"></i>', 'http://www.ncl.ac.uk/press.office/press.release/item/new-mobile-app-will-find-dementia-friendly-places', array('escape' => false, 'target' => '_blank')); ?></li>
				</ul>
			
			</div>

			<h2><?php echo __('Logos & Brand'); ?></h2>

			<div class="press-section brand-section">
				
				<p><?php echo __('Click any of the following assets to view the full-resolution version.'); ?><br / ><?php echo __('If you require any other assets please contact us.'); ?></p>

				<div class="assets-section">
					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('logo.png'), Router::url('/', true) . 'img/logo.png', array('escape' => false)); ?>
					</div>
					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('logo_square.png'), Router::url('/', true) . 'img/logo_square.png', array('escape' => false)); ?>
					</div>
					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('logo_triangle.png'), Router::url('/', true) . 'img/logo_triangle.png', array('escape' => false)); ?>
					</div>
					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('hand.png'), Router::url('/', true) . 'img/hand.png', array('escape' => false)); ?>
					</div>

					<div class="clearfix"></div>

					<hr />

					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('press-kit/brand/app-movement-text-logo-black.png'), Router::url('/', true) . 'img/press-kit/brand/app-movement-text-logo-black.png', array('escape' => false)); ?>
					</div>
					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('press-kit/brand/app-movement-text-logo-white.png'), Router::url('/', true) . 'img/press-kit/brand/app-movement-text-logo-white.png', array('escape' => false)); ?>
					</div>

					<div class="clearfix"></div>

					<hr />

					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('press-kit/brand/app-movement-brian.png'), Router::url('/', true) . 'img/press-kit/brand/app-movement-brian.png', array('escape' => false)); ?>
					</div>

					<div class="asset-tile">
						<?php echo $this->Html->link($this->Html->image('press-kit/brand/app-movement-brian-with-friends.png'), Router::url('/', true) . 'img/press-kit/brand/app-movement-brian-with-friends.png', array('escape' => false)); ?>
					</div>
					
					<div class="clearfix"></div>
					
				</div>
				
			</div>

			<h2><?php echo __('Contact'); ?></h2>

			<div class="press-section contact-section">
				
				<p><?php echo __('If you have any queries please contact us.'); ?></p>

				<ul>
					<li><span style="color:#999"><?php echo __('Press contact'); ?> : </span><a href="mailto:a.garbett@newcastle.ac.uk">a.garbett@newcastle.ac.uk</a></li>
					<li><span style="color:#999"><?php echo __('Image contact'); ?> : </span><a href="mailto:edward.jenkins@newcastle.ac.uk">edward.jenkins@newcastle.ac.uk</a></li>
				</ul>

			</div>

		</div>

		<div class="col-md-5 press-col">

			<h2><?php echo __('Case Studies'); ?></h2>

			<div class="press-section case-studies-section">

				<div class="case-study">

					<?php echo $this->Html->image('case-studies/dementia-friendly/katies-movement.jpg', array('class' => 'case-study-image')); ?>

					<div class="case-studies-caption">
						<?php echo $this->Html->link('<h3>Dementia Friendly Places</h3>', 'http://www.ncl.ac.uk/press.office/press.release/item/new-mobile-app-will-find-dementia-friendly-places', array('escape' => false, 'target' => '_blank')); ?>
						<p><?php echo __('A story about Katie Brittain\'s idea to support people living with dementia and their carers.'); ?></p>
					</div>

				</div>

			</div>

		</div>

		<div class="clearfix"></div>

	</div>

</div>