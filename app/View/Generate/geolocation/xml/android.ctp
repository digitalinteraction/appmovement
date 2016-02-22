<?php 
	$rgb = array_map('hexdec', str_split($app_colour_primary, 2));

	// Modify color
	$rgb[0] -= 20;
	$rgb[1] -= 20;
	$rgb[2] -= 20;

	// Convert back
	$app_colour_btn_hover = implode('', array_map('dechex', $rgb));
?>

<?php
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}
?>
<string name="app_name"><?php echo $app_name; ?></string>
<string name="app_identifier"><?php echo $app_identifier; ?></string>
<string name="app_type">geolocation</string>
<string name="app_movement_id"><?php echo $movement_id; ?></string>
<string name="shared_preferences_identifier">com.app_movement.geolocation.template.v1_1.app_template.<?php echo $app_identifier; ?></string>
<string name="app_version">1.0</string>

<!-- Color Palette -->
<!-- Modifiable colors -->
<color name="title">#ffffff</color>
<color name="primary"><?php echo $app_colour_primary; ?></color>
<color name="pin"><?php echo $app_colour_pin; ?></color>
<color name="star"><?php echo $app_colour_star; ?></color>

<color name="btn_hover"><?php echo adjustBrightness($app_colour_primary, -15); ?></color>

<string name="review_option_a"><?php echo $app_options[0]; ?></string>
<string name="review_option_b"><?php echo $app_options[1]; ?></string>
<string name="review_option_c"><?php echo $app_options[2]; ?></string>
<string name="review_option_d"><?php echo $app_options[3]; ?></string>