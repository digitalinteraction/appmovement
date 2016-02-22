<!-- Admin view -->

<?php
echo $this->Html->css('admin', null, array('inline'=>false));
echo $this->Html->script('jscolor/jscolor');
?>

<style type="text/css">

</style>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Admin Section'); ?></h3>

	</div>
</div>

<div class="row" id="selector-row">

	<div class="app-container">

		<div class="app-tile-wrapper">

			<div class="padder"></div>

			<?php foreach ($movements as $movement) { ?>

				<div class="app-tile" style="background-image:url('<?php echo $this->webroot . 'img/movements/medium/' . $movement["MovementPhoto"][0]["filename"]; ?>')">

				</div>

			<?php } ?>

			<div class="padder"></div>

		</div>

	</div>

</div>

<div class="row" id="configure-row">

	<div class="container">

		<div class="page">

			<div class="admin-navigation">

				<ul>
					<li><?php echo __('APP STORE'); ?></li>
					<li><?php echo __('DATABASE'); ?></li>
					<li><?php echo __('XML'); ?></li>
					<li><?php echo __('ASSETS'); ?></li>
					<li><?php echo __('LAUNCH'); ?></li>
				</ul>

			</div>

			<div class="admin-panel" id="store-panel">

				<h4><?php echo __('App Name'); ?></h4>
				<input type="text" id="dump-app-name"></input>
				<br />
				<br />
				<h4><?php echo __('App Identifier'); ?></h4>
				<input type="text" id="dump-app-identifier"></input>
				<br />
				<br />
				<h4><?php echo __('Listing'); ?></h4>
				<textarea id="dump-listing"></textarea>
				<br />
				<br />
				<h4><?php echo __('Keywords'); ?></h4>
				<input type="text" id="dump-keywords"></input>

			</div>

			<div class="admin-panel" id="db-panel">

				<h4><?php echo __('SQL Create Dump'); ?></h4>
				<textarea id="dump-sql"></textarea>

			</div>

			<div class="admin-panel" id="xml-panel">

				<h4><?php echo __('iOS XML Dump'); ?></h4>
				<textarea id="dump-xml-ios"></textarea>
				<br />
				<br />
				<h4><?php echo __('Android XML Dump'); ?></h4>
				<textarea id="dump-xml-android"></textarea>

			</div>

			<div class="admin-panel" id="assets-panel">

				<h4><?php echo __('Set Background Colour'); ?></h4>
				<input type="text" class="color" id="set-background" value=""></input>
				<div class="btn btn-warning" id="clear-background"><?php echo __('Clear'); ?></div>
				<div class="btn btn-success"id="apply-background"><?php echo __('Apply'); ?></div>
				<br />
				<br />
				<h4><?php echo __('App Icon'); ?></h4>
				<img class="generated-asset" id="app-icon" src="" style="border:#ddd 1px solid; max-width: 200px; max-height: 200px; border-radius:12px;" />
				<br />
				<br />
				<h4><?php echo __('Spash Screen'); ?></h4>
				<img class="generated-asset" id="app-splashscreen" src="" style="border:#ddd 1px solid; max-width: 320px; max-height: 320px;" />
				<br />
				<br />
				<h4><?php echo __('App Screenshots'); ?></h4>
				<img class="generated-asset" id="app-screenshot" src="" style="border:#ddd 1px solid; max-width: 320px; max-height: 320px;" />
				
				<hr />

				<div class="btn btn-success" id="download-assets-button"><?php echo __('Download Assets'); ?></div>

			</div>

			<div class="admin-panel" id="other-panel" style="text-align:center">
				<h4><?php echo __('Android Store Listing URL'); ?></h4>
				<input type="text" id="android_store_listing_url"></input>

				<h4><?php echo __('iOS Store Listing URL'); ?></h4>
				<input type="text" id="ios_store_listing_url"></input>

				<h4><?php echo __('Click below to send the launch notification to all supporters'); ?></h4>
				<div class="btn btn-danger btn-block" id="launch_btn"><?php echo __('Send Launch Notification'); ?></div>
			</div>

		</div>

	</div>

</div>

<script type="text/javascript">

$(document).ready(function() {

	var movements = <?php echo json_encode($movements); ?>;
	var currentAppIndex = 0;

	// Load default app
	if (movements.length > 0) {
		loadAppDetails(0);
	}

	$('.app-tile').click(function() {

		currentAppIndex = ($(this).index() - 1);
		loadAppDetails();
		reloadPanels();

	});

	$('#launch_btn').click(function(){
		$.ajax({
             type: "POST",
             url: 'notifications/generate/built',
             data: {movement_id : movements[currentAppIndex]["Movement"]["id"], ios_download_link : $('#ios_store_listing_url').val(), android_download_link : $('#android_store_listing_url').val()},
             success: function(data) {
	         	json = JSON.parse(data);
	         	if(json.meta.success == true)
	         	{
	         		$('#launch_btn').removeClass('btn-danger');
	         		$('#launch_btn').addClass('btn-success');
	         		$('#launch_btn').text('<?php echo __('Notifications have been sent!'); ?>');
	         		$('#launch_btn').unbind();
	         	}
             },
             error: function(error) {
             	console.log(error);
              }
        });
	});

	$('.admin-navigation li').click(function() {

		var index = $(this).index();

		$('.admin-navigation li').not(this).removeClass('active');
		$(this).addClass('active');

		$('.admin-panel').not('.admin-panel:eq(' + index + ')').hide();
		$('.admin-panel:eq(' + index + ')').show();

		// Perform actions
		switch(index) {
			case 0:
			// App Store
			generateListing();
			break;

			case 1:
			// Database
			generateSQL();
			break;

			case 2:
			// XML
			generateXML();
			break;

			case 3:
			// Assets
			generateAssets(); 
			break;

			default:
			break;
		}

	});

	$('textarea').click(function() {$(this).select(); });
	$('input').click(function() {$(this).select(); });

	$('#clear-background').click(function() { $('#assets-panel #set-background').val(''); $('#assets-panel #set-background').css('background-color', '#FFF'); generateAssets(); });
	$('#apply-background').click(function() { generateAssets(); });

	function loadAppDetails() {

		$('.app-tile').not('.app-tile:eq(' + currentAppIndex + ')').animate({'opacity':'0.3'});
		$('.app-tile:eq(' + currentAppIndex + ')').animate({'opacity':'1.0'});

		var tileWidth = 260;
		var pos = (currentAppIndex * tileWidth); 

		$('.app-tile-wrapper').animate({scrollLeft:pos});

	}

	function reloadPanels() {

		$('.admin-navigation li').removeClass('active');
		$('.admin-panel').hide();

	}

	function generateListing() {

		$('#store-panel #dump-app-name').val('<?php echo __('Loading'); ?>..');
		$('#store-panel #dump-app-identifier').val('<?php echo __('Loading'); ?>..');
		$('#store-panel #dump-listing').html('<?php echo __('Loading'); ?>..');
		$('#store-panel #dump-keywords').html('<?php echo __('Loading'); ?>..');

		var data = {'data[movement_id]': movements[currentAppIndex]['Movement']['id']};

        $.ajax({
             type: "POST",
             url: '<?php echo $this->webroot; ?>generate/listing',
             data: data,
             success: function(data) {
             	console.log(data);
	         	json = JSON.parse(data);
				$('#store-panel #dump-app-name').val(json['name']);
				$('#store-panel #dump-app-identifier').val(json['identifier']);
                $('#store-panel #dump-listing').html(json['listing']);
                $('#store-panel #dump-keywords').val(json['keywords']);
             },
             error: function(error) {
             	console.log(error);
				$('#store-panel #dump-app-name').val('<?php echo __('Something went wrong!'); ?>');
				$('#store-panel #dump-app-identifier').val('<?php echo __('Something went wrong!'); ?>');
				$('#store-panel #dump-listing').html('<?php echo __('Something went wrong!'); ?>');
				$('#store-panel #dump-keywords').html('<?php echo __('Something went wrong!'); ?>');
              }
        });

	}

	function generateSQL() {

		$('#db-panel #dump-sql').html('Loading..');

		var data = {'data[movement_id]': movements[currentAppIndex]['Movement']['id']};

        $.ajax({
             type: "POST",
             url: '<?php echo $this->webroot; ?>generate/sql',
             data: data,
             success: function(data) {
	         	json = JSON.parse(data);
                 $('#db-panel #dump-sql').html(json['sql']);
             },
             error: function(error) {
                $('#db-panel #dump-sql').html('<?php echo __('Something went wrong!'); ?>');
              }
        });

	}

	function generateXML() {

		$('#xml-panel #dump-xml-ios').html('<?php echo __('Loading'); ?>..');

		$('#xml-panel #dump-xml-android').html('<?php echo __('Loading'); ?>..');

		var data = {'data[movement_id]': movements[currentAppIndex]['Movement']['id']};

        $.ajax({
             type: "POST",
             url: '<?php echo $this->webroot; ?>generate/xml',
             data: data,
             success: function(data) {
	         	json = JSON.parse(data);
                 $('#xml-panel #dump-xml-ios').html(json['ios']);
                 $('#xml-panel #dump-xml-android').html(json['android']);
             },
             error: function(error) {
                $('#xml-panel #dump-xml-ios').html('<?php echo __('Something went wrong!'); ?>');
                $('#xml-panel #dump-xml-android').html('<?php echo __('Something went wrong!'); ?>');
              }
        });

	}

	function generateAssets() {

        $('#assets-panel #app-icon').attr('src', '');
        $('#assets-panel #app-splashscreen').attr('src', '');
        $('#assets-panel #app-screenshot').attr('src', '');

		var data = {'data[movement_id]': movements[currentAppIndex]['Movement']['id'], 'data[background]': '#' + $('#assets-panel #set-background').val()};

        $.ajax({
             type: "POST",
             url: '<?php echo $this->webroot; ?>generate/assets',
             data: data,
             success: function(data) {
             	console.log(data);
	         	json = JSON.parse(data);
                $('#assets-panel #app-icon').attr('src', base_url + json['icon']);
                $('#assets-panel #app-splashscreen').attr('src', base_url + json['splashscreen']);
                $('#assets-panel #app-screenshot').attr('src', base_url + json['screenshot']);
                $('#assets-panel #download-assets-button').click(function() {
                	document.location = base_url + json['archive'];
	            });
             },
             error: function(error) {
              }
        });

	}

});

</script>