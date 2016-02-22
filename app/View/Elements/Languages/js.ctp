<?php
$lang = [];
// share button
$lang['shareButton']['status'] = __("Support %s's app idea - %s");
$lang['shareButton']['email']['body'] = __("Hey there! \n\nI’ve found an awesome App Movement that I think you should be a part of. \n\nClick the link to find out more about this idea, \n\n%s - http://apmv.co/%s\n\nLet's start the movement!");

// design phase
$lang['designPhase']['confirmDelete'] = __("Delete contribution?");

// start movement page
$lang['startMovement']['confirm'] = __("Confirm");
$lang['startMovement']['cancel'] = __("Cancel");

// view movement page
$lang['viewMovement']['supported'] = __("Supported");
?>

<script type="text/javascript">
var lang = <?php echo json_encode($lang); ?>;
</script>