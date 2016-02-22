
<!-- Colour Swatch Element -->

<?php
echo $this->Html->css('jquery.simplecolorpicker', null, array('inline'=>false));
echo $this->Html->script('jquery.simplecolorpicker');
echo $this->Html->css('tasks/colour', null, array('inline'=>false));
?>

<div class="row">
	<div class="container contributions-container">
		<div id="contributions_wrapper" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 300, "gutter": 20, "itemSelector": ".tile" }'>
			<?php
			foreach ($contributions as $item) {
				echo $this->element('Contributions/' . $design_task["AppTypeDesignTask"]["DesignTask"]["element"], array('item' => $item, 'movement' => $movement, 'is_supporter' => $is_supporter));
			} 
			?>
		</div>
		<div class="clearfloat"></div>
	</div>
</div>

<?php if (($movement["Movement"]["phase"] == 1) && $is_supporter) { ?>

	<div class="row">
		<div class="container contribute-area">

			<div class="contribute-container">

				<div class="col-md-6 col-lg-4 visible-lg">
					
					<iframe id="iframe-map-preview" src="<?php echo Router::url('/', true); ?>/preview/geolocation_map/?primary_colour=4986e7&pin_colour=fa573c&star_colour=ffad46" height="480" width="320" border="false" frameborder="no" scrolling="no" seamless="yes"></iframe>

				</div>

				<div class="col-md-6 col-lg-4 visible-md visible-lg">
					
					<iframe id="iframe-venue-preview" src="<?php echo Router::url('/', true); ?>/preview/geolocation_venue/?primary_colour=4986e7&pin_colour=fa573c&star_colour=ffad46" height="480" width="320" border="false" frameborder="no" scrolling="no" seamless="yes"></iframe>

				</div>

				<div class="col-md-6 col-lg-4">

					<div class="clearfix"></div>
					<br />

					<h2><?php echo __('Suggest %s', __($design_task["AppTypeDesignTask"]["DesignTask"]["name"])); ?></h2>

					<div class="alert alert-warning" id="contribution-form-errors">
						<ul id="contribution-form-errors-list"></ul>
					</div>
				
					<?php echo $this->Form->create('Contribution', array('type' => 'post', 'action' => '/add', 'id' => 'contributon-add-form')); ?>

					<div class="colour-picker-label">
						<h5><?php echo __('Primary Colour'); ?></h5>
					</div>

					<div class="colour-picker-select" id="primary-picker" data-toggle="tooltip" data-placement="left" primary="primary Colour" onchange="updatePreview()">
						<select id="colorpicker-primary" name="colorpicker-picker-longlist">
							<option value="#ac725e">#ac725e</option><option value="#f83a22">#f83a22</option><option value="#fa573c">#fa573c</option><option value="#ff7537">#ff7537</option><option value="#ffad46">#ffad46</option><option value="#42d692">#42d692</option><option value="#16a765">#16a765</option><option value="#7bd148">#7bd148</option><option value="#b3dc6c">#b3dc6c</option><option value="#fbe983">#fbe983</option><option value="#fad165">#fad165</option><option value="#92e1c0">#92e1c0</option><option value="#9fe1e7">#9fe1e7</option><option value="#9fc6e7">#9fc6e7</option><option value="#4986e7">#4986e7</option><option value="#9a9cff">#9a9cff</option><option value="#b99aff">#b99aff</option><option value="#000000">#000000</option><option value="#cabdbf">#cabdbf</option><option value="#cca6ac">#cca6ac</option><option value="#f691b2">#f691b2</option><option value="#cd74e6">#cd74e6</option><option value="#a47ae2">#a47ae2</option>
						</select>
					</div>

					<div class="colour-picker-label">
						<h5><?php echo __('Pin Colour'); ?></h5>
					</div>

					<div class="colour-picker-select" id="pin-picker" data-toggle="tooltip" data-placement="top" primary="pin Colour" onchange="updatePreview()">
						<select id="colorpicker-pin" name="colorpicker-picker-longlist">
							<option value="#ac725e">#ac725e</option><option value="#f83a22">#f83a22</option><option value="#fa573c">#fa573c</option><option value="#ff7537">#ff7537</option><option value="#ffad46">#ffad46</option><option value="#42d692">#42d692</option><option value="#16a765">#16a765</option><option value="#7bd148">#7bd148</option><option value="#b3dc6c">#b3dc6c</option><option value="#fbe983">#fbe983</option><option value="#fad165">#fad165</option><option value="#92e1c0">#92e1c0</option><option value="#9fe1e7">#9fe1e7</option><option value="#9fc6e7">#9fc6e7</option><option value="#4986e7">#4986e7</option><option value="#9a9cff">#9a9cff</option><option value="#b99aff">#b99aff</option><option value="#cca6ac">#cca6ac</option><option value="#f691b2">#f691b2</option><option value="#cd74e6">#cd74e6</option><option value="#a47ae2">#a47ae2</option>
						</select>
					</div>

					<div class="colour-picker-label">
						<h5><?php echo __('Star Colour'); ?></h5>
					</div>

					<div class="colour-picker-select" id="star-picker" data-toggle="tooltip" data-placement="right" primary="star Colour" onchange="updatePreview()">
						<select id="colorpicker-star" name="colorpicker-picker-longlist">
							<option value="#ac725e">#ac725e</option><option value="#f83a22">#f83a22</option><option value="#fa573c">#fa573c</option><option value="#ff7537">#ff7537</option><option value="#ffad46">#ffad46</option><option value="#42d692">#42d692</option><option value="#16a765">#16a765</option><option value="#7bd148">#7bd148</option><option value="#b3dc6c">#b3dc6c</option><option value="#fbe983">#fbe983</option><option value="#fad165">#fad165</option><option value="#92e1c0">#92e1c0</option><option value="#9fe1e7">#9fe1e7</option><option value="#9fc6e7">#9fc6e7</option><option value="#4986e7">#4986e7</option><option value="#9a9cff">#9a9cff</option><option value="#b99aff">#b99aff</option><option value="#000000">#000000</option><option value="#cca6ac">#cca6ac</option><option value="#f691b2">#f691b2</option><option value="#cd74e6">#cd74e6</option><option value="#a47ae2">#a47ae2</option>
						</select>
					</div>

			    	<?php echo $this->Form->input('contribution_type_id', array('class' => 'input-field', 'id' => 'contribution_type_id', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["contribution_type_id"])); ?>
			    	<?php echo $this->Form->input('movement_design_task_id', array('class' => 'input-field', 'id' => 'movement_design_task_id', 'type' => 'hidden', 'value' => $design_task["MovementDesignTask"]["id"])); ?>
			    	<?php echo $this->Form->input('app_type_design_task_element', array('class' => 'input-field', 'id' => 'app_type_design_task_element', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["element"])); ?>
					
					<?php echo $this->Form->end(); ?>

					<br />

					<button class="btn btn-lg btn-success" id="contribution-add-submit"><?php echo __('Submit'); ?></button>

				</div>

				<div class="clearfix"></div>

			</div>

		</div>
	</div>

<?php } ?>

<style type="text/css">
	.modal-dialog {
		background-image: url("<?php echo Router::url('/', true); ?>img/iphone.png");
		background-repeat: no-repeat;
		background-size: 384px;
		margin-bottom: 20px;
		margin-top: 20px;
		padding: 119px 34px 115px 30px;
		width: 384px;
	}
	.modal-body {
		padding: 0px;
	}
	.modal-body iframe {
		border-radius: 4px;
		overflow: hidden;
	}
</style>

<div class="modal fade" id="preview-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<iframe id="iframe-venue-preview" src="<?php echo Router::url('/', true); ?>/preview/geolocation_venue/?primary_colour=4986e7&pin_colour=fa573c&star_colour=ffad46" height="568" width="320" border="false" frameborder="no" scrolling="no" seamless="yes"></iframe>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {

		$('#primary-picker').tooltip('show').tooltip('hide');
		$('#pin-picker').tooltip('show').tooltip('hide');
		$('#star-picker').tooltip('show').tooltip('hide');
		
		$('select[name="colorpicker-change-star-color"]').on('change', function() {
			$(document.body).css('star-color', $('select[name="colorpicker-change-star-color"]').val());
		});

		$('select[name="colorpicker-picker-longlist"]').simplecolorpicker({picker: true, theme: 'fontawesome'});
		
		$('#colorpicker-primary').simplecolorpicker('selectColor', '#4986e7');
		$('#colorpicker-pin').simplecolorpicker('selectColor', '#fa573c');
		$('#colorpicker-star').simplecolorpicker('selectColor', '#ffad46');

		$('#submit-preview-button').unbind().click(function() {

	        var contribution_id = $(this).attr("data-contribution");
	        var primary_colour = $('#colorpicker-primary').val().replace('#', '');
	        var pin_colour = $('#colorpicker-pin').val().replace('#', '');
	        var star = $('#colorpicker-star').val().replace('#', '');

			$('#iframe-map-preview').attr('src', base_url + '/preview/geolocation_map/?primary_colour=' + primary_colour + '&pin_colour=' + pin_colour + '&star_colour=' + star_colour);
	        $('#iframe-venue-preview').attr('src', base_url + '/preview/geolocation_venue/?primary_colour=' + primary_colour + '&pin_colour=' + pin_colour + '&star_colour=' + star_colour);

		});

	});

	function updatePreview() {

        var primary_colour = $('#colorpicker-primary').val().replace('#', '');
        var pin_colour = $('#colorpicker-pin').val().replace('#', '');
        var star_colour = $('#colorpicker-star').val().replace('#', '');

        $('#iframe-map-preview').attr('src', base_url + '/preview/geolocation_map/?primary_colour=' + primary_colour + '&pin_colour=' + pin_colour + '&star_colour=' + star_colour);
        $('#iframe-venue-preview').attr('src', base_url + '/preview/geolocation_venue/?primary_colour=' + primary_colour + '&pin_colour=' + pin_colour + '&star_colour=' + star_colour);
	}

</script>