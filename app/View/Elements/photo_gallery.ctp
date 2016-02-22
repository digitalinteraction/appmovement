<style type="text/css">

.photo-gallery {
	border-top: #e1e1e1 1px solid;
	padding: 0 20px;
}

.thumbnail-image {
	display: inline-block;
}

.thumbnail-image:hover {
	cursor: pointer;
}

.thumbnail-image img {
	max-height: 120px;
	margin: 24px 20px 24px 0px;
	max-width: 200px;
}

@media(max-width:968px) {

	.thumbnail-image img {
		max-height: 60px;
		max-width: 100px;
	}

}

#photo-modal img {
	width: 100%;
}

#photo-modal .close {
	height: 50px;
	width: 100%;
}

</style>

<div class="photo-gallery visible-md visible-lg">

	<?php foreach ($movement["MovementPhoto"] as $movement_photo) { ?>
		
		<div class="img-wrapper thumbnail-image" data-toggle="modal" data-target="#photo-modal">
			<?php echo $this->Html->image('movements/large/' . $movement_photo["filename"]); ?>
		</div>

	<?php } ?>

</div>

<div class="modal" id="photo-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo $this->Html->image('movements/large/' . $movement["MovementPhoto"][0]["filename"]); ?>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function() {

		$('.thumbnail-image').click(function() {

			$('#photo-modal img').attr('src', $('img', this).attr('src'));

		});

	});

</script>