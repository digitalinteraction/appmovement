<h5 class="star-rating">
	<?php for ($i = 0; $i < 5; $i++) { 
		if ($i < $rating) {
			echo '<i class="fa fa-star"></i>';
		} else {
			echo '<i class="fa fa-star-o"></i>';
		}
	} ?>
</h5>