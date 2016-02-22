<!-- Photos -->

<?php
echo $this->Html->meta(array('name' => 'og:title', 'content' => $published_app["PublishedApp"]["store_listing_name"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"]), NULL, array('inline' => false));
?>

<?php
echo $this->Html->css('published_apps/photos', null, array('inline'=>false));
echo $this->Html->script('published_apps/photos', array('inline'=>false));
?>

<div class="" id="photo-grid">

	<?php foreach ($photos as $index => $photo) { ?>
		
		<div class="photo-tile" data-filename="<?php echo $photo["Photo"]["filename"]; ?>" style="background-image:url('http://cdn.app-movement.com/apps/geolocation/uploads/small/<?php echo $photo["Photo"]["filename"]; ?>')">
		</div>
		
	<?php } ?>

	<div class="clearfloat"></div>

</div>