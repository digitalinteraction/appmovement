<!-- Template for main notifications -->

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

$siteDescription = 'App Movement ';
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $siteDescription; ?>|
		<?php echo $title_for_layout; ?>
	</title>
	<meta charset="UTF-8">
	<meta name="description" content="<?php echo __('App Movement enables anyone to propose, design and develop a mobile app. No expertise are required just a touch of determination and a great idea!'); ?>">
	<meta name="keywords" content="App-Movement, app movement, appmovement, movement app, mobile app, design a mobile app, <?php echo __('community'); ?>, <?php echo __('crowd commissioning'); ?>, <?php echo __('crowd sourcing'); ?>">
	<meta name="author" content="Open Lab, Newcastle University">
	<meta name="google-site-verification" content="5MhfYnDjbeS61_cuQe3CEPgjGKrE_NYpoZbf2ASq4PQ" />
	
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800');
		echo $this->Html->css('//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css');
		echo $this->Html->css(array('bootstrap'));
		echo $this->Html->css('main');

		// Check for RTL language
		if ($this->Session->read('Config.text_direction') == 'RTL') {
			echo $this->Html->css('rtl');
		}

		echo $this->Html->script('jquery-1.10.2.min');
		echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js');
		echo $this->Html->script('main');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

		echo $this->Html->script('sprintf');
		echo $this->element('Languages/js');
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

	<div class="page-wrap">
		
		<?php echo $this->element('navigation'); ?>

		<div id="content">

			<style type="text/css">

			.email-container {
				border: #e1e1e1 1px solid;
				margin: 20px auto !important;
				max-width: 992px !important;
				overflow: hidden;
			}
			.email-container .email-body {
				background-color: #fff;
			}
			.email-container .email-footer {
				border-bottom: none !important;
				display: none;
			}
			@media(max-width: 992px) {
				.email-container {
					border: none;
					border-bottom: #e1e1e1 1px solid;
					border-radius: 0px;
					margin: 0px !important;
					max-width: 992px !important;
					overflow: hidden;
				}
			}
			.language-selector-container {
				margin: 20px auto;
				padding: 10px;
				text-align: center;
			}
			</style>

			<div class="language-selector-container">
				<?php echo $this->Element('language_selector'); ?>
			</div>

			<?php echo $this->fetch('content'); ?>
			
		</div>

	</div>

	<?php echo $this->element('footer'); ?>

	<?php 
	echo $this->Html->script('jquery-1.10.2.min');
	echo $this->Html->script(array('bootstrap.min'));
	?>

</body>
</html>