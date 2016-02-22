<!-- Start view -->

<?php
echo $this->Html->css('start', null, array('inline'=>false));
echo $this->Html->script('movements/start');

echo $this->Html->css('file_uploader/jquery.fileupload', null, array('inline'=>false));

echo $this->Html->script('file_uploader/vendor/jquery.ui.widget');
echo $this->Html->script('file_uploader/jquery.iframe-transport');
echo $this->Html->script('file_uploader/jquery.fileupload');
?>

<script type="text/javascript">

	var app_type_names = ["<?php echo __('Geolocation Review'); ?>", "<?php echo __('Social Network'); ?>", "<?php echo __('Information'); ?>", "<?php echo __('Events'); ?>"];
	var app_type_descriptions = ["<?php echo __('A Geolocation Review app allows users to rate and review anything with a fixed location. An example might be a cafe, a public park or somewhere to lock your bicycle. Anyone can add new points of interest to the map and reviews on their own and other people\'s contributions.'); ?>", "<?php echo __('A \'Social Network\' is a type of application that allows people to connect with one another another. It enables users to share content such as photos, videos, comments and other media.'); ?>", "<?php echo __('An \'Information\' app provides a quick method of accessing detailed information about a specific subject. It can be useful in a variety of situations where a little extra information is key.'); ?>", "<?php echo __('Our lives are full of exciting events, an \'Event\' app can help people to keep track of what events are taking place in the future. Such an app could also act as a discovery tool for say a recent fan or enthusiast.'); ?>"];
	var app_type_examples = ["<li><?php echo __('The best places to feed ducks'); ?></li><li><?php echo __('Restaurants that cater for nut allergies'); ?></li><li><?php echo __('Amazing street art in London'); ?></li><li><?php echo __('Public toilet finder'); ?></li>", "<li><?php echo __('Sports coach network'); ?></li><li><?php echo __('A super social network for teenager'); ?>s</li><li><?php echo __('Dog owners social network'); ?></li><li><?php echo __('Young mothers support network'); ?></li>", "<li><?php echo __('Miami tourist guide'); ?></li><li><?php echo __('Help guide for your new computer'); ?></li><li><?php echo __('Top tips for looking after your children'); ?></li>", "<li><?php echo __('Live gigs for the Artic Monkeys'); ?></li><li><?php echo __('Car boot sales in the North East'); ?></li><li><?php echo __('Stand up comedy events in London'); ?></li>"];

</script>

<!-- Chat widget -->
<!-- <script type='text/javascript'>(function () { var done = false;var script = document.createElement('script');script.async = true;script.type = 'text/javascript';script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript';document.getElementsByTagName('HEAD').item(0).appendChild(script);script.onreadystatechange = script.onload = function (e) {if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {var w = new PCWidget({ c: '446d2697-659b-4601-8ee0-ef8def8a773e', f: true });done = true;}};})();</script> -->

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Start a Movement'); ?></h3>

	</div>
</div>

<div class="row start-row">
	<div class="container">

		<div class="col-md-9">

			<?php echo $this->Form->create('Movement', array('class' => 'movement-form', 'novalidate', 'type' => 'file')); ?>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('App Type'); ?><i class="fa fa-check-circle"></i></label>
			    	<div class="clearfix"></div>
					<ul class="type-selector" id="type-selector">
						<?php
						$index = 0;
						foreach ($app_types as $app_type) {
							$index++;
							echo '<li class="available-type" data-index="' . $index . '">' . __($app_type) . '</li>';
						}
						echo '<li class="unavailable-type" data-index="' . ($index + 1) . '">' . __('Social Network') . '</li>';
						echo '<li class="unavailable-type" data-index="' . ($index + 2) . '">' . __('Information') . '</li>';
						echo '<li class="unavailable-type" data-index="' . ($index + 3) . '">' . __('Events') . '</li>';
						echo '<li class="unavailable-type" data-index="' . ($index + 4) . '">' . __('Other') . '</li>';
						echo $this->Form->input('app_type_id', array('id' => 'app-type-input', 'type' => 'text', 'style' => 'display: none !important; opacity: 0;', 'label' => false));
						?>
					</ul>
				</div>
		    	<div class="input-footer">
		    		<?php echo __('Select the type of application you wish to launch.'); ?>
		    	</div>
			</div>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Title'); ?><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="title_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('title', array('class' => 'input-field', 'id' => 'input-title', 'label' => false, 'placeholder' => __('Movement Title'), 'onKeyUp' => 'textCounter(this,"title_count",50,false)', 'onBlur' => 'textCounter(this,"title_count",50,true)', 'onFocus' => 'textCounter(this,"title_count",50,false)')); ?>
				</div>
		    	<div class="input-footer">
		    		<?php echo __('Give your movement a clear and catchy title.'); ?>
		    	</div>
			</div>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Short Description'); ?><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="description_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('description', array('class' => 'input-field', 'id' => 'input-description', 'label' => false, 'placeholder' => __('Describe your idea in one sentence'), 'rows' => '4', 'type' => 'textarea', 'onKeyUp' => 'textCounter(this,"description_count",140,false)', 'onBlur' => 'textCounter(this,"description_count",140,true)', 'onFocus' => 'textCounter(this,"description_count",140,false)')); ?>
			    </div>
		    	<div class="input-footer">
		    		<?php echo __('A short summary of the movement that will be visible across the site.'); ?>
		    	</div>
			</div>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Photo'); ?><i class="fa fa-check-circle"></i></label>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('photo', array('type' => 'hidden', 'id' => 'movement-image', 'label' => false)); ?>
			    
				    <!-- The fileinput-button span is used to style the file input field as button -->
				    <span class="btn btn-success fileinput-button" id="fileinput-button">
				        <span id="upload-button-text"><?php echo __('Choose Photo'); ?></span>
				        <!-- The file input field used as target for the file upload widget -->
				        <input id="fileupload" type="file" name="files[]">
				    </span>
				    <br>
				    <br>

				    <!-- The global progress bar -->
				    <div id="progress" class="progress">
				        <div class="progress-bar progress-bar-success"></div>
				    </div>

				    <!-- Photo errors -->

				    <div class="error-message" id="photo-error-message" style="display:<?php if(isset($errors['photo'])) { echo 'block'; } else { echo 'none'; } ?>"><?php if(isset($errors['photo'])) { foreach ($errors['photo'] as &$error) { echo $error; } } ?></div>

				    <!-- The container for the uploaded files -->
				    <div id="files" class="files"></div>

				</div>
		    	<div class="input-footer">
		    		<?php echo __('A striking image that represents your movement.'); ?>
		    	</div>
			</div>

			<div class="input-section visibily-hidden">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Location'); ?> <span style="color:#CCC; font-weight:normal">(<?php echo __('Optional'); ?>)</span><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="location_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('location', array('class' => 'input-field', 'id' => 'input-location', 'label' => false, 'placeholder' => __('Movement Location'), 'onKeyUp' => 'textCounter(this,"location_count",40,false)', 'onBlur' => 'textCounter(this,"location_count",40,true)', 'onFocus' => 'textCounter(this,"location_count",40,false)')); ?>
				</div>
		    	<div class="input-footer">
		    		<?php echo __('Provide the location of your movement.'); ?>
		    	</div>
			</div>

			<?php if ($this->Session->read('Config.language') == 'en') { ?>
				
				<!-- Hide tags for non-english (crappy JS library) -->

				<div class="input-section">
					<div class="input-wrapper">
						<label class="start-input-label"><?php echo __('Tags'); ?> <span style="color:#CCC; font-weight:normal">(<?php echo __('Optional'); ?>)</span><i class="fa fa-check-circle"></i></label>
						<div class="clearfix"></div>
						<?php echo $this->Form->input('tags', array('style' => 'display:none', 'label' => 'Tags', 'type' => 'hidden')); ?>
					</div>
			    	<div class="input-footer">
			    		<?php echo __('Add some tags to your movement to help people discover it.'); ?>
			    	</div>
				</div>

			<?php } ?>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Overview'); ?><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="overview_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('overview', array('class' => 'input-field', 'id' => 'input-overview', 'label' => false, 'placeholder' => __('A longer overview of your movement'), 'rows' => '10', 'type' => 'textarea', 'onKeyUp' => 'textCounter(this,"overview_count",4000,false)', 'onBlur' => 'textCounter(this,"overview_count",4000,true)', 'onFocus' => 'textCounter(this,"overview_count",4000,false)')); ?>

			    	<?php
					$options = array(
					    'label' => __('Next Step'),
					    'class' => 'btn btn-success pull-right',
					    'id' => 'continue-button'
					);
					echo $this->Form->end($options);
					?>
					<div class="clearfloat"></div>
				</div>

			</div>

		</div>

		<div class="movement-preview col-md-3">

			<div class="movement-tile">

				<div class="inner-container">

					<div class="movement-photo" id="preview-image">
						<i class="fa fa-picture-o fa-3x"></i>
					</div>

					<div class="tile-content">

						<div class="tile-description">

							<h3 class="movement-title" id="preview-title"></h3>
							
							<p class="movement-creator">

							<p class="movement-description" id="preview-description"></p>
							
						</div>

						<div class="tile-footer">

							<h5 style="margin:5px;"><?php echo __('Movement Preview'); ?></h5>

							<div class="clearfix"></div>

						</div>

					</div>

				</div>
				
			</div>

		</div>

	</div>
</div>

<!-- App Type Modal -->
<div class="modal fade app-type-modal" id="app-type-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body text-center">
				<h3 id="app-type-modal-name"></h3>
				<h5 id="app-type-development" style="color:#f00"><?php echo __('Not Currently Available'); ?></h5>
				<hr />
				<p id="app-type-modal-description"></p>
				<hr />
				<div class="examples">
					<h5><?php echo __('Example Applications'); ?></h5>
					<ul>
						<div id="app-type-modal-examples"></div>
					</ul>
				</div>
            </div>
			<div class="modal-footer">
				<div class="btn btn-success btn-sm" id="app-type-modal-dismiss-button" data-dismiss="modal"><?php echo __('Done'); ?></div>
			</div>
		</div>
	</div>
</div>

<!-- App Type Suggest Modal -->
<div class="modal fade app-type-modal" id="app-type-suggest-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body text-center">
				<h3><?php echo __('Suggest App Type'); ?></h3>
				<hr />
				<p><?php echo __('We would like to provide you with more templates in the future, please submit your app type ideas below to help us to provide more relevant app types.'); ?></p>
				<br />
				<div class="input-wrapper">
			    	<?php echo $this->Form->input('type_suggestion', array('type' => 'text', 'class' => 'input-field', 'id' => 'input-type-suggestion', 'label' => false, 'placeholder' => __('Your Suggestion'))); ?>
				</div>
            </div>
			<div class="modal-footer">
				<div class="btn btn-success btn-sm" onClick="submitSuggestion()"><?php echo __('Done'); ?></div>
			</div>
		</div>
	</div>
</div>

<?php if (!$this->Session->read('Auth.User')) { ?>
	
	<!-- Login Modal -->
	
	<?php echo $this->element('Modals/auth_modal', array('auto_show' => true)); ?>

<?php } ?>

<script type="text/javascript">

	$('#start-tab li').addClass('active');

	function submitSuggestion() {

		// retrieve suggestion
		var suggestion = $('#input-type-suggestion').val();

		// clear suggestion
		// $('#input-type-suggestion').val() = '';

		// check if suggestion was made
		if (suggestion != "") {

			// suggestion made
			console.log('Suggestion - ' + suggestion);

			$.getJSON(base_url + "suggest/<?php echo $this->Session->read('Auth.User.id'); ?>/" + suggestion, function( data ) {
				// Suggested
				if (data) {
					
					$('#input-type-suggestion').val('');
					$('#app-type-suggest-modal').modal('hide');
					
				}
			});

		} else {

			// no suggestion made
			$('#app-type-suggest-modal').modal('hide');
		}

	}

	$(document).ready(function(){
		var offset = $('.movement-tile').offset();  
		var nav_height = 80;
		offset.top -= nav_height;


		$(window).scroll(function () {  
			var scrollTop = $(window).scrollTop(); // check the visible top of the browser  

			console.log(scrollTop);
			console.log(offset.top);
			if(offset.top < (scrollTop + 20))
			{
				$('.movement-tile').addClass('fixed');  	
			} 
			else
			{
				$('.movement-tile').removeClass('fixed');  
			}
		}); 
		
	});

	/*jslint unparam: true */
	/*global window, $ */
	$(function () {
	    'use strict';
	    // Change this to the location of your server-side upload handler:
	    var url = '<?php echo Router::url('/', true); ?>img/';
	    $('#fileupload').fileupload({
	        url: url,
	        dataType: 'json',
	        done: function (e, data) {
	            $.each(data.result.files, function (index, file) {
	                
	                console.log(file);

	            	if (file.error) {

		            	$('#upload-button-text').html('<?php echo __('Failed'); ?>');
		            	$('#fileinput-button').removeClass('btn-success').addClass('btn-danger');

		            	setTimeout(function() {
		            		$('#upload-button-text').html('<?php echo __('Choose Photo'); ?>');
		            	 	$('#fileinput-button').removeClass('btn-danger').addClass('btn-success');
		            	 }, 2000);

	            		$('#photo-error-message').html(file.error).css('display', 'block');

		                $('#files').html('');
			            $('#preview-image').css('background-image', 'url()');
			            $('#movement-image').val('');

			            $('#progress .progress-bar').css('width', '0%');

	            	} else {

		            	$('#upload-button-text').html('<?php echo __('Uploaded'); ?>');
		            	$('#fileinput-button').removeClass('btn-danger').addClass('btn-success');

		            	setTimeout(function() {
		            		$('#upload-button-text').html('<?php echo __('Choose Photo'); ?>');
		            	 	$('#fileinput-button').removeClass('btn-danger').addClass('btn-success');
		            	 }, 2000);

	            		$('#photo-error-message').html('').css('display', 'none');

		                $('#files').html('<img src="https://app-movement.com/img/movements/small/'+file.name+'" style="display:inline-block; margin-right: 10px; max-height:80px; max-width:80px;" />');
			            $('#preview-image').css('background-image', 'url(https://app-movement.com/img/movements/medium/'+file.name+')');
			            $('#movement-image').val(file.name);

	            	}
	            });
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css('width', progress + '%');
	            if (progress < 100) {
	            	$('#upload-button-text').html('<?php echo __('Uploading'); ?>');
		            	$('#fileinput-button').removeClass('btn-danger').addClass('btn-success');
	            }
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});

</script>