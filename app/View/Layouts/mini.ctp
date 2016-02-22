<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $published_app["PublishedApp"]["app_name"]; ?> |
		<?php echo $title_for_layout; ?>
	</title>
	<meta charset="UTF-8">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="google-site-verification" content="5MhfYnDjbeS61_cuQe3CEPgjGKrE_NYpoZbf2ASq4PQ" />
	
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800');
		echo $this->Html->css('//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css');
		echo $this->Html->css(array('bootstrap'));
		echo $this->Html->css('main');

		echo $this->Html->script('jquery-1.10.2.min');
		echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js');
		echo $this->Html->script('main');
		echo $this->Html->script('masonry');
		echo $this->Html->script('imagesloaded');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>

<body>
	<script type="text/javascript">
		var base_url = "<?php echo Router::url('/', true); ?>";
	</script>
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-44963053-3', 'app-movement.com');
	  ga('send', 'pageview');

	</script>

	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '1623248021226561',
	      xfbml      : true,
	      version    : 'v2.2'
	    });
	  };

	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>

	<?php
		echo $this->Session->flash();
		echo $this->Session->flash('good');
		echo $this->Session->flash('bad');
	?>

	<div class="page-wrap">
		
		<?php echo $this->element('PublishedApps/navigation'); ?>

		<div id="content">

			<?php echo $this->fetch('content'); ?>
			
		</div>

	</div>

	<?php 
	echo $this->Html->script('jquery-1.10.2.min');
	echo $this->Html->script(array('bootstrap.min'));
	?>

</body>
</html>
