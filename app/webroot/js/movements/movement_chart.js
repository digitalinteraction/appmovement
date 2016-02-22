/* Supporters Chart */

var c;
var ct;
var ctx;
var activity_chart;

$(document).ready(function() {

	loadChart();

});

function loadChart() {

	Chart.defaults.global.responsive = true;

	var ctx = document.getElementById("activity-chart").getContext("2d");

	$.getJSON(base_url + "stats/" + movement_id, function( data ) {

		label_array = [];
		data_array = [];
		var max = 1;

		for(var k in data)
		{
			label_array.push(k);
			data_array.push(data[k]);
			if (data[k] > max) { max = data[k]; }
		}

		// console.log(label_array);
		// console.log(data_array);

		var data = {
		labels : label_array,
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "#4CC5FF",
				pointColor : "#4CC5FF",
				pointStrokeColor : "#fff",
				data : data_array
			}
		]
	};

	var options = {
		showScale : false,
		scaleShowGridLines : false,
	    scaleGridLineWidth : 0,
	    bezierCurve : false,
	    bezierCurveTension : 0.0,
	    pointDot : false,
	    pointDotRadius : 2,
	    pointDotStrokeWidth : 1,
	    pointHitDetectionRadius : 50,
	    datasetStroke : true,
	    datasetStrokeWidth : 2,
	    datasetFill : false,
	    tooltipTemplate : "<%if (label != 0){%><%if (value == 0){%>No New Supporters<%}else{%>Day <%=label%> - <%= value %> Supporter<%=(value==1)?'':'s'%><%}%><%}else{%>Movement Launched!<%}%>",
	    pointHitDetectionRadius : 1,
	    scaleOverride: true,
	    scaleSteps: 14,
	    scaleStepWidth: (max / 10),
	    scaleStartValue: -((max / 10) * 2),
	};

	c = $('#activity-chart');
	ct = c.get(0).getContext('2d');
	ctx = document.getElementById("activity-chart").getContext("2d");

	activity_chart = new Chart(ct).Line(data, options);
	});


}