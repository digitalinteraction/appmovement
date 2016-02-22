<!-- Published Apps -->

<style type="text/css">

/* Published app grid */

#published-app-container ul {
	list-style: none;
	margin: 0;
	padding: 0;

	overflow: hidden;
}
#published-app-container ul li {
	list-style: none;
	margin: 0;
	padding: 0;

	position: relative;

	float: left;
	text-align: center;
	width: 25%;
}

/* Published app tile */

.published-app-tile {
	padding: 30px;
	text-align: center;
}
.published-app-tile .app-icon {
	background-color: #fff;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
	border: #ddd 1px solid;
	border-radius: 36px;
	height: 160px;
	margin: 20px auto;
	width: 160px;
}

.published-app-tile h4 {
	color: #666;
	font-weight: 200;
}

.published-app-tile p {
	color: #999;
	font-size: 14px;
	font-weight: 200;
}
.published-app-tile p .fa-circle {
	color: #91e43c;
}

.published-app-tile.available:hover {
	background-color: #f6f6f6;
	cursor: pointer;
}
.published-app-tile.available:hover h4 {
	color: #2CD7D7;
}

.grid-border {
    position: absolute;
    top: 0px;
    left: -1px;
    width: 1000%;
    height: 1000%;
    border-left: 1px solid #e1e1e1;
    border-top: 1px solid #e1e1e1;
    pointer-events: none;
}

</style>

<div id="published-app-container">

	<ul>
		<?php foreach ($published_apps as $published_app) { ?>

			<li><?php echo $this->element('app_tile', array('published_app' => $published_app)); ?></li>
			
		<?php } ?>
	</ul>

</div>

<script type="text/javascript">

$(document).ready(function() {

	$('.published-app-tile').click(function() {

		if ($(this).hasClass('available')) {

			window.location = $(this).attr('data-url');
		
		};

	});

});

</script>