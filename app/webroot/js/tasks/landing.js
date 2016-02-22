/* Tasks Landing */

var tour;
var storage

$(document).ready(function() {

	if (show_tour) {
		storage = false;
	} else {
		storage = window.localStorage;
	}

	// Instance the tour
	tour = new Tour({
	  steps: [
	  {
	    element: ".banner-row",
	    title: "Welcome",
	    content: "This is the design area, from here you can influence the design of the app.",
	    placement: "bottom"
	  },
	  {
	    element: ".design-header",
	    title: "Movement Panel",
	    content: "This panel shows the status of the design phase and gives you helpful tips.",
	    placement: "bottom"
	  },
	  {
	    element: "#movement-task-0",
	    title: "Tasks",
	    content: "You can contribute to tasks by voting or sharing ideas.",
	    placement: "top"
	  },
	  {
	    element: ".discussion-area",
	    title: "Discussion",
	    content: "Start a conversation here.",
	    placement: "top"
	  },
	  {
	    title: "Tour Complete",
	    content: "You have completed the tour!"
	  }
	],
	backdrop: true,
	storage: storage,
	template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-info btn-block' style='' id='continue-btn' data-role='next'>Continue</button><button class='btn btn-default' style='display:none' data-role='end'>End tour</button></div></div>",
	onShown: function (tour) { checkIfLastStep(); }
	});

	// Initialize the tour
	tour.init();

	tour.start();

});

// Check if last step
function checkIfLastStep() {
	if (tour.getCurrentStep() == 3) {
		$('#continue-btn').html('End Tour');
	}
	if (tour.getCurrentStep() == 5) {
		tour.end();
		// if creator than show support modal
		if (is_creator && !has_supported) {
			$('#support-modal').modal('show');
		}
	}
	console.log(tour.getCurrentStep());
}