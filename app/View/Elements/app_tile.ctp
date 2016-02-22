
<!-- App Tile Element -->

<div class="grid-border"></div>

<div class="published-app-tile <?php if ($published_app["PublishedApp"]["published"]) { echo 'available'; } ?>" data-url="<?php echo $this->Html->url(array('controller' => 'published_apps', 'action' => 'download', $published_app["PublishedApp"]["id"])); ?>">

	<div class="app-icon" style="background-image:url('<?php echo $this->webroot; ?>img/published-apps/icons/large/<?php echo $published_app["PublishedApp"]["app_icon"]; ?>')"></div>

	<h4><?php echo $published_app["PublishedApp"]["app_name"]; ?></h4>
	
	<?php if ($published_app["PublishedApp"]["published"]) { ?>
		<p><i class="fa fa-circle"></i> Available</p>
	<?php } else { ?>
		<p><i class="fa fa-circle-o"></i> Coming Soon</p>
	<?php } ?>

</div>