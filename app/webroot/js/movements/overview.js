/* Overview Movements */

$(document).ready(function() {

	$('#filter-everything').click(function() { filterMovements('everything'); });
	$('#filter-support').click(function() { filterMovements('support'); });
	$('#filter-design').click(function() { filterMovements('design'); });
	$('#filter-launch').click(function() { filterMovements('launch'); });

	var $container = $('#movements-container');

	$container.imagesLoaded( function() {

		$container.masonry();

	});

});

function filterMovements(passedClass) {
	
	 $('.filter-button').removeClass('active');

    switch (passedClass) { 
        
        case 'everything':

        	$('.phase--1').stop().animate({opacity: 1.0}, 500);
        	$('.phase-0').stop().animate({opacity: 1.0}, 500);
			$('.phase-1').stop().animate({opacity: 1.0}, 500);
			$('.phase-2').stop().animate({opacity: 1.0}, 500);
			$('#filter-everything').addClass('active');
        
            break;
        
        case 'support':

        	$('.phase--1').stop().animate({opacity: 0.1}, 500);
        	$('.phase-0').stop().animate({opacity: 1.0}, 500);
			$('.phase-1').stop().animate({opacity: 0.1}, 500);
			$('.phase-2').stop().animate({opacity: 0.1}, 500);
			$('#filter-support').addClass('active');
        
            break;
        
        case 'design':

        	$('.phase--1').stop().animate({opacity: 0.1}, 500);
        	$('.phase-0').stop().animate({opacity: 0.1}, 500);
			$('.phase-1').stop().animate({opacity: 1.0}, 500);
			$('.phase-2').stop().animate({opacity: 0.1}, 500);
			$('#filter-design').addClass('active');
        
            break;
        
        case 'launch':

        	$('.phase--1').stop().animate({opacity: 0.1}, 500);
        	$('.phase-0').stop().animate({opacity: 0.1}, 500);
			$('.phase-1').stop().animate({opacity: 0.1}, 500);
			$('.phase-2').stop().animate({opacity: 1.0}, 500);
			$('#filter-launch').addClass('active');
        
            break;
    }

}