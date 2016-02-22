
<?php
echo $this->Html->css('finalcountdown/demo.css');

echo $this->Html->script('kinetic.js');
echo $this->Html->script('jquery.final-countdown.min.js');


?>

<style>
*{color:#E1E1E1 !important;}
.banner-row{border-bottom: 0px;}
.countdown-container {margin-top: 300px;}
</style>
<script>
var now = new Date().getTime();
$(document).ready(function() {
$('.countdown').final_countdown({
start: '1396438627',
end: '1402228800',
now: new Date().getTime() / 1000,
selectors: {
    value_seconds: '.clock-seconds .val',
    canvas_seconds: 'canvas_seconds',
    value_minutes: '.clock-minutes .val',
    canvas_minutes: 'canvas_minutes',
    value_hours: '.clock-hours .val',
    canvas_hours: 'canvas_hours',
    value_days: '.clock-days .val',
    canvas_days: 'canvas_days'
},
seconds: {
    borderColor: '#7995D5',
    borderWidth: '10'
},
minutes: {
    borderColor: '#ACC742',
    borderWidth: '10'
},
hours: {
    borderColor: '#ECEFCB',
    borderWidth: '10'
},
days: {
    borderColor: '#FF9900',
    borderWidth: '10'
}}, function() {
// Finish callback
});
});
</script>
<div class="banner-row">
	<div class="container">

		<h3>Welcome to App-Movement!</h3>
		<h3>Coming soon!</h3>
		<p>Sign up below for news and updates on our launch date</p>
		
		<div class="countdown countdown-container">
		    <div class="clock row">
		        <div class="clock-item clock-days countdown-time-value col-sm-6 col-md-3">
		            <div class="wrap">
		                <div class="inner">
		                    <div id="canvas_days" class="clock-canvas"></div>

		                    <div class="text">
		                        <p class="val">0</p>
		                        <p class="type-days type-time">DAYS</p>
		                    </div><!-- /.text -->
		                </div><!-- /.inner -->
		            </div><!-- /.wrap -->
		        </div><!-- /.clock-item -->

		        <div class="clock-item clock-hours countdown-time-value col-sm-6 col-md-3">
		            <div class="wrap">
		                <div class="inner">
		                    <div id="canvas_hours" class="clock-canvas"></div>

		                    <div class="text">
		                        <p class="val">0</p>
		                        <p class="type-hours type-time">HOURS</p>
		                    </div><!-- /.text -->
		                </div><!-- /.inner -->
		            </div><!-- /.wrap -->
		        </div><!-- /.clock-item -->

		        <div class="clock-item clock-minutes countdown-time-value col-sm-6 col-md-3">
		            <div class="wrap">
		                <div class="inner">
		                    <div id="canvas_minutes" class="clock-canvas"></div>

		                    <div class="text">
		                        <p class="val">0</p>
		                        <p class="type-minutes type-time">MINUTES</p>
		                    </div><!-- /.text -->
		                </div><!-- /.inner -->
		            </div><!-- /.wrap -->
		        </div><!-- /.clock-item -->

		        <div class="clock-item clock-seconds countdown-time-value col-sm-6 col-md-3">
		            <div class="wrap">
		                <div class="inner">
		                    <div id="canvas_seconds" class="clock-canvas"></div>

		                    <div class="text">
		                        <p class="val">0</p>
		                        <p class="type-seconds type-time">SECONDS</p>
		                    </div><!-- /.text -->
		                </div><!-- /.inner -->
		            </div><!-- /.wrap -->
		        </div><!-- /.clock-item -->
		    </div><!-- /.clock -->
		</div><!-- /.countdown-wrapper -->
	</div>
</div>