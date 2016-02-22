<!-- Mobile Survey Complete view -->

<?php
echo $this->Html->css('mobile/global', null, array('inline'=>false));
?>

<style type="text/css">

	.success-circle {
		position: absolute;
		top: 50%;
		left: 50%;
		margin-top: -50px;
		margin-left: -50px;

		background-color: #5BCA5B;
		border-radius: 50px;
		line-height: 120px;
		height: 100px;
		text-align: center;
		width: 100px;
	}
	.success-circle i {
		color: #fff;
		font-size: 40px;
	}

</style>

<div id="complete-container">
	<div class="success-circle">
		<i class="fa fa-check"></i>
	</div>
</div>