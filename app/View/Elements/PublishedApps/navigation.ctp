<!-- Navigation -->

<?php echo $this->Html->css('published_apps/navigation', null, array('inline'=>true)); ?>

<div class="row" id="navigation">
	<div class="container">

		<div id="logo">
			<?php echo $this->Html->link($published_app["PublishedApp"]["app_name"], array('controller' => 'published_apps', 'action' => 'index'), array('escape' => false)); ?>
		</div>

		<ul>
			<?php
			echo $this->Html->link('<li>Apps</li>', array('controller' => 'published_apps', 'action' => 'index'), array('escape' => false));
			echo $this->Html->link('<li>Map</li>', array('controller' => 'published_apps', 'action' => 'map', $published_app["PublishedApp"]["id"]), array('escape' => false));
			echo $this->Html->link('<li>Photos</li>', array('controller' => 'published_apps', 'action' => 'photos', $published_app["PublishedApp"]["id"]), array('escape' => false));
			echo $this->Html->link('<li>Stats</li>', array('controller' => 'published_apps', 'action' => 'stats', $published_app["PublishedApp"]["id"]), array('escape' => false));
			echo $this->Html->link('<li>Download</li>', array('controller' => 'published_apps', 'action' => 'download', $published_app["PublishedApp"]["id"]), array('escape' => false));
			?>
		</ul>

	</div>
</div>