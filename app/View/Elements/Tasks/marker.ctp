
<!-- Marker Task Element -->

<?php
echo $this->Html->css('jquery.simplecolorpicker', null, array('inline'=>false));
echo $this->Html->script('jquery.simplecolorpicker');
echo $this->Html->css('tasks/marker', null, array('inline'=>false));?>

<div class="row">
	<div class="container">
		<div class="preview-options-container">

			<h4><?php echo __('Preview colour'); ?></h4>
			<form>
			<div class="colour-picker-select" id="pin-picker" data-toggle="tooltip" data-placement="left" title="Pin Colour" onchange="updatePreview()">
				<select id="colorpicker-pin" name="colorpicker-change-pin-color" onchange="updatePreview()">
					<option value="#ac725e">#ac725e</option><option value="#f83a22">#f83a22</option><option value="#fa573c">#fa573c</option><option value="#ff7537">#ff7537</option><option value="#ffad46">#ffad46</option><option value="#42d692">#42d692</option><option value="#16a765">#16a765</option><option value="#7bd148">#7bd148</option><option value="#b3dc6c">#b3dc6c</option><option value="#fbe983">#fbe983</option><option value="#fad165">#fad165</option><option value="#92e1c0">#92e1c0</option><option value="#9fe1e7">#9fe1e7</option><option value="#9fc6e7">#9fc6e7</option><option value="#4986e7">#4986e7</option><option value="#9a9cff">#9a9cff</option><option value="#b99aff">#b99aff</option><option value="#cabdbf">#cabdbf</option><option value="#cca6ac">#cca6ac</option><option value="#f691b2">#f691b2</option><option value="#cd74e6">#cd74e6</option><option value="#a47ae2">#a47ae2</option>
				</select>
			</div>
		</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="container contributions-container">

		<div id="contributions_wrapper" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 270, "gutter": 20, "itemSelector": ".tile" }'>
			<?php
			foreach ($contributions as $item) {
				echo $this->element('Contributions/' . $design_task["AppTypeDesignTask"]["DesignTask"]["element"], array('item' => $item, 'movement' => $movement, 'is_supporter' => $is_supporter));
			} 
			?>
		</div>
		<div class="clearfloat"></div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function () {

	$('#colorpicker-pin').simplecolorpicker('selectColor', '#7bd148');

});

function updatePreview() {

		var colour = $('#colorpicker-pin').val().replace('#', '');

		$('.marker-image').each(function(index) {

			$(this).attr('src', $(this).attr('data-url') + '?colour=' + colour);

		});
}

</script>