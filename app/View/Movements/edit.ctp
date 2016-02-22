<!-- Edit view -->

<?php
echo $this->Html->css('start', null, array('inline'=>false));
echo $this->Html->script('movements/edit');

echo $this->Html->css('file_uploader/jquery.fileupload', null, array('inline'=>false));

echo $this->Html->script('file_uploader/vendor/jquery.ui.widget');
echo $this->Html->script('file_uploader/jquery.iframe-transport');
echo $this->Html->script('file_uploader/jquery.fileupload');
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Edit Movement'); ?></h3>

	</div>
</div>

<div class="row start-row">
	<div class="container">

		<div class="col-md-9">

			<?php echo $this->Form->create('Movement', array('class' => 'movement-form', 'novalidate', 'type' => 'file')); ?>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Title'); ?><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="title_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('title', array('value' => $movement["Movement"]["title"], 'class' => 'input-field', 'id' => 'input-title', 'label' => false, 'placeholder' => __('Movement Title'), 'onKeyUp' => 'textCounter(this,"title_count",50,false)', 'onBlur' => 'textCounter(this,"title_count",50,true)', 'onFocus' => 'textCounter(this,"title_count",50,false)')); ?>
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
			    	<?php echo $this->Form->input('description', array('value' => $movement["Movement"]["description"], 'class' => 'input-field', 'id' => 'input-description', 'label' => false, 'placeholder' => __('Movement Description'), 'rows' => '4', 'type' => 'textarea', 'onKeyUp' => 'textCounter(this,"description_count",140,false)', 'onBlur' => 'textCounter(this,"description_count",140,true)', 'onFocus' => 'textCounter(this,"description_count",140,false)')); ?>
			    </div>
		    	<div class="input-footer">
		    		<?php echo __('A short summary of the movement that will be visible across the site.'); ?>
		    	</div>
			</div>

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Photo'); ?><i class="fa fa-check-circle"></i></label>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('photo', array('type' => 'hidden', 'value' => $movement["MovementPhoto"][0]["filename"], 'id' => 'movement-image', 'label' => false)); ?>
			    
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

			<div class="input-section">
				<div class="input-wrapper">
					<label class="start-input-label"><?php echo __('Location'); ?> <span style="color:#CCC; font-weight:normal">(<?php echo __('Optional'); ?>)</span><i class="fa fa-check-circle"></i></label>
			    	<span class="char_counter" id="location_count"></span>
			    	<div class="clearfix"></div>
			    	<?php echo $this->Form->input('location', array('value' => $movement["Movement"]["location"], 'class' => 'input-field', 'id' => 'input-location', 'label' => false, 'placeholder' => __('Movement Location'), 'onKeyUp' => 'textCounter(this,"location_count",40,false)', 'onBlur' => 'textCounter(this,"location_count",40,true)', 'onFocus' => 'textCounter(this,"location_count",40,false)')); ?>
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
						<?php echo $this->Form->input('tags', array('value' => $movement["Movement"]["tags"], 'style' => 'display:none', 'label' => __('Tags'), 'type' => 'hidden')); ?>
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
			    	<?php echo $this->Form->input('overview', array('value' => $movement["Movement"]["overview"], 'class' => 'input-field', 'id' => 'input-overview', 'label' => false, 'placeholder' => __('A longer overview of your movement'), 'rows' => '10', 'type' => 'textarea', 'onKeyUp' => 'textCounter(this,"overview_count",4000,false)', 'onBlur' => 'textCounter(this,"overview_count",4000,true)', 'onFocus' => 'textCounter(this,"overview_count",4000,false)')); ?>
			    	
			    	<?php
					$options = array(
					    'label' => __('Save Changes'),
					    'class' => 'btn btn-success pull-right'
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

					<div class="movement-photo" id="preview-image" style="background-image:url(<?php echo '../../img/movements/medium/' . $movement["MovementPhoto"][0]["filename"]; ?>)"></div>

					<div class="tile-content">

						<div class="tile-description">

							<h3 class="movement-title" id="preview-title"><?php echo $movement["Movement"]["title"]; ?></h3>
							
							<p class="movement-creator"><?php echo __('by %s', $movement["User"]["fullname"]); ?></p>

							<p class="movement-description" id="preview-description"><?php echo $movement["Movement"]["description"]; ?></p>
							
						</div>

						<div class="tile-footer">

							<h5 style="margin:5px;"><?php echo __('Movement Preview'); ?></h5>

							<div class="clearfix"></div>

						</div>

					</div>

				</div>
				
			</div>

			<?php if (($movement["Movement"]["supporters_count"] < 2) && ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"]))
			{
			echo $this->Html->link( '<div class="btn btn-danger btn-block start-input-label">' . __('Delete Movement') . '</div>', array('controller' => 'movements', 'action' => 'delete/' . $movement["Movement"]["id"]), array('escape' => false) );
			}
			?>


		</div>

	</div>
</div>

<script type="text/javascript">

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
