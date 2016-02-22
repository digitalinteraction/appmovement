<!-- Marker maker view -->

<div class="row banner-row">
	<div class="container">
		<h3>Marker Maker</h3>
	</div>
</div>

<style type="text/css">

.marker-container {
	padding: 40px;
	text-align: center;
}

.marker-container img {
	height: 200px;
}

.controls-container {
	padding-top: 40px;
	text-align: center;
}

</style>

<div class="row">

	<div class="container">

		<div class="page-panel">

			<div class="marker-container">

				<img id="marker-image" src="">

			</div>

			<div class="controls-container">

				<div class="btn btn-success btn-lg" id="generate-btn"><?php echo __('Generate Random Pin'); ?></div>

			</div>

		</div>

	</div>

</div>\

<script type="text/javascript">

$(document).ready(function() {

	generatePin(); // Load first marker

	$('#generate-btn').click(function() {

		generatePin();

	});
	

});

var count = 0;

function generatePin() {

	var pin_styles = ['a', 'b', 'c', 'd', 'e'];
	var pin_style = pin_styles[Math.floor(Math.random() * pin_styles.length)];

	count++;

	r = Math.sin(2 * count + 2) * 255;
	g = Math.sin(2 * count + 0) * 255;
	b = Math.sin(2 * count + 4) * 255;

	if (count > 20) { count = 0};

	pin_colour = componentToHex(r) + componentToHex(g) + componentToHex(b);

	$("#marker-image").attr('src', base_url + '/generate/marker/' + pin_style + '/' + pin_colour + '/true');

}

function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

</script>