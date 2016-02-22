<!-- Discover view -->

<?php
echo $this->Html->css('discover', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="custom-container">

		<h3><?php echo __('Discover'); ?></h3>

	</div>
</div>

<div class="row sort-row">
	<div class="custom-container">

		<ul>
	        <?php 
	        echo '<li>' . $this->Html->link(__('Everything'), '/discover') . '</li>';
	        echo '<li>' . $this->Paginator->sort('created', __('Recent'), array('direction' => 'desc')) . '</li>';
	        echo '<li>' . $this->Paginator->sort('supporters_count', __('Popular'), array('direction' => 'desc')) . '</li>';
	        ?>


			<li class="search-element">
				<?php echo $this->Form->create('Movement', array('class' => 'search-form', 'id' => 'search-form', 'action' => 'index', 'type' => 'get')); ?>

					<?php echo $this->Form->input('query', array('class' => 'form-control', 'placeholder' => __('Search Movements'), 'label' => false, 'value' => $query)); ?>
					
				<?php echo $this->Form->end(); ?>
			</li>

	        <li class="search-element">
	        	<i class="fa fa-search" id="search-button"></i>
	        </li>
    	</ul>

	</div>
</div>

<div class="row movements-row">
	<div class="custom-container">

		<?php if (count($movements) == 0) {
			echo '<div class="no-results"><h3>' . __('No Movements Found') . '</h3><h5>' . __('Please search again') . '</h5></div>';
		} ?>

		<div id="movements-container" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 265, "gutter": 20, "itemSelector": ".movement-tile" }'>

			<?php foreach ($movements as &$movement) {

				echo $this->element('movement_tile', array('movement' => $movement));

			} ?>

		</div>

	</div>
</div>

<div class="row">
	<div class="custom-container">

		<div class="paginator-container">
			<ul class="pager">
				<?php
				echo $this->Paginator->prev('<i class="fa fa-long-arrow-left fa-2x"></i>', array(
				    'separator' => '',
				    'disabledTag' => 'disabled',
				    'class' => 'previous',
				    'tag' => 'li',
				    'escape' => false
			    ));
				echo $this->Paginator->next('<i class="fa fa-long-arrow-right fa-2x"></i>', array(
				    'separator' => '',
				    'disabledTag' => 'disabled',
				    'class' => 'next',
				    'tag' => 'li',
				    'escape' => false
			    ));
				?>
			</ul>
		</div>

	</div>
</div>

<script type="text/javascript">

	$('#discover-tab li').addClass('active');

	$('#search-button').click(function() {
		$('#search-form').submit();
	});
	
	var $container = $('#container');

	$container.imagesLoaded( function() {
	
		$container.masonry();

		$container.masonry('on', 'layoutComplete', function(msnryInstance, laidOutItems) {

			// Layout complete

		});
	
	});

</script>