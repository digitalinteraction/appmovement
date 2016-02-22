<!-- Published App Stats -->

<?php
echo $this->Html->meta(array('name' => 'og:title', 'content' => $published_app["PublishedApp"]["store_listing_name"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/published-apps/icons/large/' . $published_app["PublishedApp"]["app_icon"]), NULL, array('inline' => false));
?>

<?php
echo $this->Html->css('published_apps/stats', null, array('inline'=>false));
echo $this->Html->script('published_apps/stats', array('inline'=>false));
echo $this->Html->script('chart', array('inline' => true));
?>

<div class="row">

	<div class="container">

		<div class="stats-tile" id="active-user-count">

			<h2><?php echo $total_users_count; ?></h2>
			<h5>Registered Users</h5>
		</div>	

		<div class="stats-tile" id="total-venues-count">

			<h2><?php echo $total_venues_count; ?></h2>
			<h5>Added Venues</h5>

		</div>	

		<div class="stats-tile" id="total-reviews-count">

			<h2><?php echo $total_reviews_count; ?></h2>
			<h5>Added Reviews</h5>

		</div>	

	</div>

</div>

<div class="row">

	<div class="container" id="chart-container">

		<canvas id="review-chart" height="100"></canvas>

		<div id="legend"></div>

	</div>

</div>

<script type="text/javascript">

var ctx = document.getElementById("review-chart").getContext("2d");
var chart;

$(document).ready(function() {

	var review_data = <?php echo $review_counts; ?>;
	var venue_data = <?php echo $venue_counts; ?>;
	var user_data = <?php echo $user_counts; ?>;

	label_array = [];
	review_data_array = [];
	venue_data_array = [];
	user_data_array = [];
	var max = 1;

	for(var k in review_data)
	{
		label_array.push(k);
		review_data_array.push(review_data[k]);
		if (review_data[k] > max) { max = review_data[k]; }
	}

	max = 1;

	for(var j in venue_data)
	{
		// label_array.push(j);
		venue_data_array.push(venue_data[j]);
		if (venue_data[j] > max) { max = venue_data[j]; }
	}

	max = 1;

	for(var l in user_data)
	{
		// label_array.push(l);
		user_data_array.push(user_data[l]);
		if (user_data[l] > max) { max = user_data[l]; }
	}

	var data = {
	    labels: label_array,
	    datasets: [
	        {
	            label: "Reviews",
	            fillColor: "rgba(255,66,0,0.0)",
	            strokeColor: "rgba(255,66,0,1)",
	            pointColor: "rgba(255,66,0,1)",
	            pointStrokeColor: "#fff",
	            pointHighlightFill: "#fff",
	            pointHighlightStroke: "rgba(255,66,0,1)",
	            data: review_data_array
	        },
	        {
	            label: "Venues",
	            fillColor: "rgba(0,215,86,0.0)",
	            strokeColor: "rgba(0,215,86,1)",
	            pointColor: "rgba(0,215,86,1)",
	            pointStrokeColor: "#fff",
	            pointHighlightFill: "#fff",
	            pointHighlightStroke: "rgba(0,215,86,1)",
	            data: venue_data_array
	        }
	    ]
	};
	
	console.log(venue_data_array);

	chart = new Chart(ctx).Line(data, { bezierCurve: true, responsive: true, scaleShowLabels: false, showScale: true, customTooltips: true, tooltipTemplate: "<%if (label){%><%=label%>-- <%}%><%= value %>" });

	$(window).resize(function() {

		chart.resize();

	});

});

</script>