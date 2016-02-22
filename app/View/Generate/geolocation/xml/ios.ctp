
// iOS XML

"app_name" = "<?php echo $app_name; ?>";
"app_logo" = "<?php echo $app_logo; ?>";
"app_identifier" = "<?php echo $app_identifier; ?>";
"movement_id" = "<?php echo $movement_id; ?>";
"ga_identifier" = "UA-XXXXXXXX-X";

"movement_url" = "https://app-movement.com/view/<?php echo $movement_id; ?>";

"app_version" = "1.3";
"api_version" = "1.0";
"api_key" = "<?php echo Configure::read('geolocation.api_key'); ?>";

"global_salt" = "<?php echo Configure::read('Security.salt'); ?>";

"auth_base_url" = "https://app-movement.com/api/auth";
"app_base_url" = "https://app-movement.com/api/geolocation/<?php echo $app_identifier; ?>";

// Foursquare Configuration

"foursquare_base_url" = "https://api.foursquare.com";
"foursquare_client_id" = "<?php echo Configure::read('geolocation.foursquare_client_id'); ?>";
"foursquare_client_secret" = "<?php echo Configure::read('geolocation.foursquare_client_secret'); ?>";
"foursquare_version" = "<?php echo Configure::read('geolocation.foursquare_version'); ?>";

// Review Options

"OptionA" = "<?php echo $app_options[0]; ?>";
"OptionB" = "<?php echo $app_options[1]; ?>";
"OptionC" = "<?php echo $app_options[2]; ?>";
"OptionD" = "<?php echo $app_options[3]; ?>";

// Theme Colors

"PrimaryColor" = "<?php echo $app_colour_primary; ?>";
"PinColor" = "<?php echo $app_colour_pin; ?>";
"StarColor" = "<?php echo $app_colour_star; ?>";
