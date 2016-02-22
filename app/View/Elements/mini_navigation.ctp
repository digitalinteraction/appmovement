<!-- Navigation -->

<?php echo $this->Html->css('published_apps/navigation', null, array('inline'=>true)); ?>

<div class="row" id="navigation">
	<div class="container">

		<div id="logo">
			<?php echo $this->Html->link('<strong>' . $published_app["PublishedApp"]["name"] . '</strong>', array('controller' => 'pages', 'action' => 'display', 'home'), array('escape' => false)); ?>
		</div>

		<ul>
			<?php
			echo $this->Html->link('<li>Map</li>', array('controller' => 'published_apps', 'action' => 'analytics', $published_app["PublishedApp"]["id"]), array('escape' => false, 'id' => 'analytics-tab'));
			echo $this->Html->link('<li>Download</li>', array('controller' => 'published_apps', 'action' => 'download', $published_app["PublishedApp"]["id"]), array('escape' => false, 'id' => 'download-tab'));
			?>
		</ul>

	</div>
</div>