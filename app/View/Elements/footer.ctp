<!-- Footer -->

<style type="text/css">

	.language-selector {
		background-color: #fff;
		border: #e1e1e1 1px solid;
		border-radius: 4px;
		color: #000;
		height: 36px;
		outline: none;
	}

</style>

<div class="row footer">
	<div class="container">

		<ul class="site-links">
			<li>
				App Movement ©<?php echo date("Y"); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('Contact'), array('controller' => 'pages', 'action' => 'display', 'contact')); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('FAQ'), array('controller' => 'pages', 'action' => 'display', 'faq')); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('Terms'), array('controller' => 'pages', 'action' => 'display', 'terms')); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('Privacy'), array('controller' => 'pages', 'action' => 'display', 'privacy')); ?>
			</li>
			<li>
				<?php echo $this->Html->link(__('Press'), array('controller' => 'pages', 'action' => 'display', 'press')); ?>
			</li>
			<li>
				<?php echo $this->Element('language_selector'); ?>
			</li>
		</ul>

		<ul class="social-links">

			<a class="university-link" href="https://di.ncl.ac.uk/" target="blank">
				<li>
					<?php echo $this->Html->image('newcastle-university.png'); ?>
				</li>
			</a>

		</ul>

	</div>
</div>