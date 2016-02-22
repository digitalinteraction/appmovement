<!-- Published App Download -->

<?php
echo $this->Html->meta(array('name' => 'og:title', 'content' => $published_app["PublishedApp"]["store_listing_name"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'google-play-app', 'content' => 'com.culturelab.feedfinder'), NULL, array('inline' => false));
?>

<?php
echo $this->Html->css('published_apps/download', null, array('inline'=>false));
echo $this->Html->script('published_apps/download', array('inline'=>false));
?>
<div class="row">

	<div class="container">

		<div class="download-container">

			<?php echo $this->Html->image('published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"], array('class' => 'app-icon')); ?>

			<h2><?php echo $published_app["PublishedApp"]["store_listing_name"]; ?></h2>

			<div class="top-store-image">

			<h5 class="text-muted">Available for Free</h5>

			<?php echo $this->Html->link($this->Html->image('apple-store.jpg', array('class' => 'store-image')), $published_app["PublishedApp"]["ios_download_link"], array('escape' => false)); ?>

			<?php echo $this->Html->link($this->Html->image('android-store.jpg', array('class' => 'store-image')), $published_app["PublishedApp"]["android_download_link"], array('escape' => false)); ?>

			</div>

			<br />

			<p><?php echo $published_app["PublishedApp"]["store_listing_description"]; ?></p>

			<br />

			<h5 class="text-muted">Available for Free</h5>

			<br />

			<?php echo $this->Html->link($this->Html->image('apple-store.jpg', array('class' => 'store-image')), $published_app["PublishedApp"]["ios_download_link"], array('escape' => false)); ?>

			<?php echo $this->Html->link($this->Html->image('android-store.jpg', array('class' => 'store-image')), $published_app["PublishedApp"]["android_download_link"], array('escape' => false)); ?>

		</div>

	</div>

</div>
