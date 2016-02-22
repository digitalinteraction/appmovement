<?php
if (strtolower($this->Session->read('Config.language')) == "ar") {
	echo $this->Html->link( '<div class="video-banner fancybox-media" id="video-banner"><p>' . __('Watch the introduction video') . '</p><div id="close-banner-button"><i class="fa fa-times-circle fa-lg"></i></div></div>', 'https://www.youtube.com/watch?v=mOKNHTWhtJQ', array('class' => 'fancybox-media', 'escape' => false) );
} else {
	echo $this->Html->link( '<div class="video-banner fancybox-media" id="video-banner"><p>' . __('Watch the introduction video') . '</p><div id="close-banner-button"><i class="fa fa-times-circle fa-lg"></i></div></div>', 'https://www.youtube.com/watch?v=F6wOuj-6CB0', array('class' => 'fancybox-media', 'escape' => false) );
}
?>