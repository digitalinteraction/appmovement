<?php
App::import('Vendor', 'imageColor');
App::uses('ConnectionManager', 'Model');

class GenerateController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');

    public $uses = array('Movement', 'Contribution', 'MovementDesignTask', 'PublishedApp');

    public $autoRender = false;
    public $layout = 'ajax';

    public function listing() {

        $movement_id = $this->request->data('movement_id');
        $parameters = $this->getParameters($movement_id);

        $movement = $this->Movement->findById($this->request->data('movement_id'));

        $listing_view = new View($this, false);
        $listing_view->viewPath = 'Generate/geolocation/';
        $listing_view->set($parameters);
        $listing_output = $listing_view->render('listing');

        $response = array('name' => $parameters['app_name'], 'identifier' => $movement['Movement']['identifier'], 'listing' => $listing_output, 'keywords' => $movement['Movement']['tags']);
        echo json_encode($response);
    }

    public function sql() {

        $movement = $this->Movement->findById($this->request->data('movement_id'));

        $sql_view = new View($this, false);
        $sql_view->viewPath = 'Generate/geolocation/';
        $sql_view->set('app_identifier', $movement["Movement"]["identifier"]);
        $sql_output = $sql_view->render('sql');

        $response = array('sql' => $sql_output);
        echo json_encode($response);
    }

    public function xml() {

        $movement_id = $this->request->data('movement_id');
        
        $parameters = $this->getParameters($movement_id);

        $movement = $this->Movement->findById($movement_id);

        // iOS
        $xml_ios_view = new View($this, false);        
        $xml_ios_view->viewPath = 'Generate/geolocation/xml/';
        $xml_ios_view->set($parameters);

        $xml_ios_view->set('movement_id', $movement_id);
        $xml_ios_view->set('app_identifier', $movement["Movement"]["identifier"]);
        $xml_ios_output = $xml_ios_view->render('ios');

        // Android
        $xml_android_view = new View($this, false);        
        $xml_android_view->viewPath = 'Generate/geolocation/xml/';
        $xml_android_view->set($parameters);

        $xml_android_view->set('movement_id', $movement_id);
        $xml_android_view->set('app_identifier', $movement["Movement"]["identifier"]);
        $xml_android_output = $xml_android_view->render('android');

        $response = array('ios' => $xml_ios_output, 'android' => $xml_android_output);
        echo json_encode($response);
    }

    public function fetch_config($published_app_id, $generate_key, $force = false) {

        $this->autoRender = false;
        $this->layout = 'ajax';

        // Check dev key
        if ($generate_key != Configure::read('geolocation.generate_key')) {
            
            $response['meta']['success'] = false;

            // Send response
            echo json_encode($response);
        }

        $published_app = $this->PublishedApp->findById($published_app_id);

        if (!$published_app || $force)
        {
            $published_app = $this->generate_app($published_app_id);
        }

        $published_app["PublishedApp"]["app_configuration"] = json_decode($published_app["PublishedApp"]["app_configuration"]);

        $response['meta']['success'] = true;
        $response['response'] = $published_app;

        // Send response
        echo json_encode($response);
    }

    public function assets() {

        $movement_id = $this->request->data('movement_id');

        $background = $this->request->data('background');
        
        if (!$background)
        {
            return false;
        }

        // Get winning logo
        $parameters = $this->getParameters($movement_id);
        
        // Convert to correct size and place on background
        $imageurl = Router::url('/', true) . '/img/contributions/large/' . $parameters['app_logo'];

        if (exif_imagetype($imageurl) == IMAGETYPE_GIF) {
            $source_img = imagecreatefromgif ($imageurl); // Get source
        }

        if (exif_imagetype($imageurl) == IMAGETYPE_PNG) {
            $source_img = imagecreatefrompng($imageurl); // Get source
        }

        if (exif_imagetype($imageurl) == IMAGETYPE_JPEG) {
            $source_img = imagecreatefromjpeg($imageurl); // Get source
        }

        $icon_padding = 15;
        $splash_padding = 20;
        $screenshot_padding = 20;

        $files = glob('img/assets/*'); // get all file names
        foreach($files as $file){ // iterate files
          if (is_file($file))
            unlink($file); // delete file
        }

        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $app_icon_url = 'img/assets/icon_' . $movement_id . '_' . $random . '.png'; // Set save url
        imagepng($this->generateAsset($source_img, $background, 400, 400, $icon_padding), $app_icon_url); // Save

        $app_splashscreen_url = 'img/assets/splashscreen_' . $movement_id . '_' . $random . '.png'; // Set save url
        imagepng($this->generateAsset($source_img, $background, 640, 960, $splash_padding), $app_splashscreen_url); // Save

        $app_screenshot_url = 'img/assets/screenshot_' . $movement_id . '_' . $random . '.png'; // Set save url
        imagepng($this->generateAsset($source_img, $background, 960, 640, $screenshot_padding), $app_screenshot_url); // Save

        $icon_urls = $this->generateIconAssets($movement_id, $source_img, $background, $icon_padding);
        $splash_urls = $this->generateSplashAssets($movement_id, $source_img, $background, $splash_padding);
        $screenshot_urls = $this->generateScreenshotAssets($movement_id, $source_img, $background, $screenshot_padding);

        $asset_urls = array_merge($icon_urls, $splash_urls, $screenshot_urls);

        // Compress
        $archive_url = 'img/assets/assets_' . $movement_id . '.zip';

        $this->archiveAssets($asset_urls, $archive_url, true);

        $response = array('icon' => $app_icon_url, 'splashscreen' => $app_splashscreen_url, 'screenshot' => $app_screenshot_url, 'archive' => $archive_url);
        echo json_encode($response);
    }

    public function generateIconAssets($movement_id, $source_img, $background, $padding) {

        $asset_sizes = array(array('29', '29'), array('40', '40'), array('50', '50'), array('57', '57'), array('58', '58'), array('60', '60'), array('72', '72'), array('76', '76'), array('80', '80'), array('100', '100'), array('114', '114'), array('120', '120'), array('144', '144'), array('152', '152'), array('256', '256'), array('512', '512'), array('1024', '1024'));

        $asset_urls = array();

        foreach ($asset_sizes as $size) {
            $asset_url = 'img/assets/icon_' . $movement_id . '_' . $size[0] . 'x' . $size[1] . '.png';
            imagepng($this->generateAsset($source_img, $background, $size[0], $size[1], $padding), $asset_url);
            array_push($asset_urls, $asset_url);
        }

        return $asset_urls;
    }

    public function generateSplashAssets($movement_id, $source_img, $background, $padding) {

        $asset_sizes = array(array('320', '480'), array('640', '960'), array('640', '1136'));

        $asset_urls = array();

        foreach ($asset_sizes as $size) {
            $asset_url = 'img/assets/splash_' . $movement_id . '_' . $size[0] . 'x' . $size[1] . '.png';
            imagepng($this->generateAsset($source_img, $background, $size[0], $size[1], $padding), $asset_url);
            array_push($asset_urls, $asset_url);
        }

        return $asset_urls;
    }

    public function generateScreenshotAssets($movement_id, $source_img, $background, $padding) {

        $asset_sizes = array(array('480', '320'), array('960', '640'), array('1136', '640'));

        $asset_urls = array();

        foreach ($asset_sizes as $size) {
            $asset_url = 'img/assets/screenshot_' . $movement_id . '_' . $size[0] . 'x' . $size[1] . '.png';
            imagepng($this->generateAsset($source_img, $background, $size[0], $size[1], $padding), $asset_url);
            array_push($asset_urls, $asset_url);
        }

        return $asset_urls;
    }

    public function archiveAssets($files = array(), $destination = null, $overwrite = false) {

        if (file_exists($destination) && !$overwrite) { return false; }

        $valid_files = array();

        if (is_array($files)) {
                
                foreach($files as $file) {
                        if (file_exists($file)) {
                                $valid_files[] = $file;
                        }
                }
        }

        if (count($valid_files)) {
                
                $zip = new ZipArchive();
                
                if ($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                        return false;
                }
                
                foreach($valid_files as $file) {
                        $new_filename = substr($file,strrpos($file,'/') + 1);
                        $zip->addFile($file,$new_filename);
                }
                
                //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
                
                $zip->close();
                
                return file_exists($destination);
        }
        else
        {
                return false;
        }
    }

    public function generateAsset($source_img, $background, $height, $width, $padding) {

        // Calculate padding and sides

        if ($height < $width) {
            $max_side = $height;
            $padding = ($height / 100) * $padding;
        } else {
            $max_side = $width;
            $padding = ($width / 100) * $padding;
        }

        $source_image_height = imagesy($source_img);
        $source_image_width = imagesx($source_img);

        if ($source_image_height < $source_image_width) {
            $ratio_height = $source_image_height / $source_image_width;
            $ratio_width = 1;
        } else {
            $ratio_width = $source_image_width / $source_image_height;
            $ratio_height = 1;
        }

        $source_target_height = ($max_side - ($padding * 2)) * $ratio_height;
        $source_target_width = ($max_side - ($padding * 2)) * $ratio_width;

        $source_gd_image = imagecreatetruecolor($source_target_width, $source_target_height);
        imagecopyresampled($source_gd_image, $source_img, 0, 0, 0, 0, $source_target_width, $source_target_height, $source_image_width, $source_image_height);
        
        $dest_image = imagecreatetruecolor($height, $width); // width, height
        

        if ($background == '#') {
            $imageColor = new imageColor;
            $color = $imageColor->averageBorder($source_img);
            $background_color = imagecolorallocate($dest_image, $color['red'], $color['green'], $color['blue']); // Set background
        } else {
            list($r, $g, $b) = sscanf($background, "#%02x%02x%02x");
            $background_color = imagecolorallocate($dest_image, $r, $g, $b); // Set background
        }
        
        imagefill($dest_image, 0, 0, $background_color);

        // Copy and merge
        $padding_left = (($height - $source_target_width) / 2);
        $padding_top = (($width - $source_target_height) / 2);

        imagecopy($dest_image, $source_gd_image, $padding_left, $padding_top, 0, 0, $source_target_width, $source_target_height);

        return $dest_image;
    }

    public function getParameters($movement_id) {

        $design_tasks = $this->MovementDesignTask->find('all', array(
            'conditions' => array(
                'MovementDesignTask.movement_id' => $movement_id,
                ),
            'contain' => array('AppTypeDesignTask' => array('DesignTask' => 'ContributionType'))
            ));

        // Loop design tasks and get winners
        
        $app_name = 'default';
        $app_logo = 'default';
        $app_colour_primary = 'default';
        $app_colour_pin = 'default';
        $app_colour_star = 'default';
        $app_options = array('default', 'default', 'default', 'default');

        foreach ($design_tasks as $design_task) {

            switch ($design_task["AppTypeDesignTask"]["DesignTask"]["ContributionType"]["type"]) {
                
                case 'name':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'name')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_name = $contribution_data->name;
                    }       
                    break;

                case 'logo':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'logo')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_logo = $contribution_data->url;
                    }
                    break;

                case 'colour':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'colour')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_colour_primary = $contribution_data->primary;
                        $app_colour_pin = $contribution_data->pin;
                        $app_colour_star = $contribution_data->star;
                    }
                    break;

                case 'options':
                    $contributions = $this->Contribution->find('all', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'options'),
                        'limit' => 4
                        ));

                    if (count($contributions) >= 4) {

                        $index = 0;

                        foreach ($contributions as $contribution) {

                            $contribution_data = json_decode($contribution["Contribution"]["data"]);

                            $app_options[$index] = ucfirst($contribution_data->name);

                            $index++;
                        }
                    }
                    break;
            }

        }

        $movement = $this->Movement->findById($movement_id);

        $parameters = array(
            'app_identifier' => $movement['Movement']['identifier'],
            'app_name' => $app_name,
            'app_logo' => $app_logo,
            'app_colour_primary' => $app_colour_primary,
            'app_colour_pin' => $app_colour_pin,
            'app_colour_star' => $app_colour_star,
            'app_options' => $app_options
        );

        return $parameters;
    }

    public function marker_maker()
    {
        $this->autoRender = true;
        $this->layout = 'default';
    }

    public function marker($marker_style = 'a', $trim = true) {
        
        $marker_style = strtoupper($marker_style); // Save lowercase queries
        
        $colour = (isset($_GET['colour'])) ? $_GET['colour'] : '#ff0000';

        $height = 360;
        $width = 320;

        // Create a blank image
        $image = imagecreatetruecolor($width, $height);

        // Allocate colours
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);
        $grey = imagecolorallocate($image, 190, 190, 190);
        $red = imagecolorallocate($image, 255, 0, 0);

        if ($trim == true)
        {
            imagefilledrectangle($image, 0, 0, $width, $height, $white);
        }
        else
        {
            // Make background transparent
            imagecolortransparent($image, $black);
        }

        $colour_array = $this->hex2rgb($colour);
        $pin_colour = imagecolorallocate($image, $colour_array[0], $colour_array[1], $colour_array[2]); // Set pin colour

        switch ($marker_style) {

            case 'A':

                // Draw stalk
                $points = array(
                    44, 250,
                    ($width - 44), 250,
                    ($width / 2), $height
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $pin_colour);

                // Draw the circle
                imagefilledellipse($image, ($width / 2), ($width / 2) + 10, $width - 40, $width - 40, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, ($width / 2), ($width / 2) + 10, 150, 150, $white);

            break;

            case 'B':

                // Draw stalk
                $points = array(
                    58, 150,
                    ($width - 58), 150,
                    ($width / 2), $height
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 160, 110, $width - 100, $width - 100, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 160, 110, 120, 120, $white);

            break;

            case 'C':

                // Draw stalk
                $points = array(
                    25, 102,
                    ($width - 25), 102,
                    ($width / 2), $height,
                    ($width / 2), $height
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $pin_colour);

                // Draw stalk
                $points = array(
                    100, 0,
                    ($width - 100), 0,
                    ($width / 2), $height,
                    ($width / 2), $height
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 95, 75, 150, 150, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, ($width - 95), 75, 150, 150, $pin_colour);

                // Draw the circle
                // imagefilledellipse($image, 160, $height - 15, 30, 30, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 160, 110, 120, 120, $white);
            
            break;

            case 'D':

                // Draw stalk
                $points = array(
                    147, 190,
                    173, 190,
                    173, ($height - 12),
                    147, ($height - 12)
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $grey);

                // Draw the circle
                imagefilledellipse($image, 160, $height - 15, 26, 26, $grey);

                // Draw the circle
                imagefilledellipse($image, 160, 120, 150, 150, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 130, 90, 35, 35, $white);

            break;

            case 'E':

                // Draw stalk
                $points = array(
                    160, 150,
                    210, 150,
                    175, $height - 14,
                    160, $height - 14
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $grey);

                // Draw stalk
                $points = array(
                    110, 150,
                    160, 150,
                    160, $height - 14,
                    145, $height - 14
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $this->adjustBrightness($image, $grey, -15));

                // Draw the circle
                imagefilledellipse($image, ($width / 2), $height - 15, 28, 28, $grey);

                // Draw the arc
                imagefilledarc($image, ($width / 2), $height - 15, 28, 28, 90, 270, $this->adjustBrightness($image, $grey, -15), IMG_ARC_PIE);

                // Draw the circle
                imagefilledellipse($image, ($width / 2), 110, 220, 220, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, ($width / 2), 110, 130, 130, $white);

            break;

            case 'F':

                // Draw stalk
                $points = array(
                    60, 180,
                    ($width - 60), 180,
                    ($width / 2), $height
                );

                imagefilledpolygon($image, $points, (count($points) / 2), $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 160, 135, $width - 100, $width - 100, $pin_colour);

                // Draw the circle
                imagefilledellipse($image, 160, 135, $width - 160, $width - 160, $this->adjustBrightness($image, $pin_colour, -15));

                // Draw the circle
                imagefilledellipse($image, 160, 135, 120, 120, $white);

            break;

        }

        

        if ($trim == true)
        {
            // Trim transparent pixels

            $trim_colour = 0xFFFFFF;

            $space_top = 0;
            $space_bottom = 0;
            $space_left = 0;
            $space_right = 0;

            // Find top space
            for (; $space_top < imagesy($image); ++$space_top)
            {
              for ($x = 0; $x < imagesx($image); ++$x)
              {
                if (imagecolorat($image, $x, $space_top) != $trim_colour)
                {
                   break 2;
                }
              }
            }

            // Find bottom space
            for (; $space_bottom < imagesy($image); ++$space_bottom)
            {
              for ($x = 0; $x < imagesx($image); ++$x)
              {
                if (imagecolorat($image, $x, imagesy($image) - $space_bottom-1) != $trim_colour)
                {
                   break 2;
                }
              }
            }

            // Find left space
            for (; $space_left < imagesx($image); ++$space_left)
            {
              for ($y = 0; $y < imagesy($image); ++$y)
              {
                if (imagecolorat($image, $space_left, $y) != $trim_colour)
                {
                   break 2;
                }
              }
            }

            // Find right space
            for (; $space_right < imagesx($image); ++$space_right)
            {
              for ($y = 0; $y < imagesy($image); ++$y)
              {
                if (imagecolorat($image, imagesx($image) - $space_right-1, $y) != $trim_colour)
                {
                   break 2;
                }
              }
            }

            $new_height = imagesy($image)-($space_top+$space_bottom);
            $new_width = imagesx($image)-($space_left+$space_right);

            $resampled_image = imagecreatetruecolor($new_width, $new_height);
            
            // Make background transparent
            imagecolortransparent($resampled_image, $black);

            // Copy image
            imagecopyresampled($resampled_image, $image, -$space_left, -$space_top, 0, 0, $new_width + ($space_left + $space_right), $new_height + ($space_top + $space_bottom), $width, $height);

            // Output the picture to the browser
            header('Content-type: image/png');

            imagepng($resampled_image);
            imagedestroy($resampled_image);

        } else {

            // Output the picture to the browser
            header('Content-type: image/png');

            imagepng($image);
            imagedestroy($image);

        }
    }

    function hex2rgb($hex) {
       
       $hex = str_replace("#", "", $hex);

       if (strlen($hex) == 3)
       {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       }
       else
       {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       
       $rgb = array($r, $g, $b);
       
       return $rgb;
    }

    function adjustBrightness($image, $colour, $steps) {

        $rgb = imagecolorsforindex($image, $colour);

        $colour_parts = array($rgb['red'], $rgb['green'], $rgb['blue']);

        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        foreach ($colour_parts as $index => $colour)
        {
            $colour   = max(0, min(255, $colour + $steps)); // Adjust colour
            $colour_parts[$index] = $colour;
        }

        $dark_colour = imagecolorallocate($image, $colour_parts[0], $colour_parts[1], $colour_parts[2]);
        
        return $dark_colour;
    }

    public function isAuthorized($user) {
	
        // Restricted to admin
        if ((!isset($user['role']) || ($user['role'] != 'admin')) && (!in_array($this->action, array('marker', 'fetch_config')))) {

            return false;
        }

        return parent::isAuthorized($user);
    }

    function beforeFilter() 
    {
        parent::beforeFilter();

    	if (in_array($this->action, array('marker', 'fetch_config'))) {

    		$this->Auth->allow();

    	}
    }

    public function generate_app($movement_id, $dev_key) {

        $movement = $this->Movement->findById($movement_id);

        if (!$movement) {
            $response['meta']['success'] = false;
            
            // Send response
            return json_encode($response);
        }

        $design_tasks = $this->MovementDesignTask->find('all', array(
            'conditions' => array(
                'MovementDesignTask.movement_id' => $movement_id,
                ),
            'contain' => array('AppTypeDesignTask' => array('DesignTask' => 'ContributionType'))
            ));

        // Loop design tasks and get winners
        
        $app_name = '';
        $app_logo = '';
        $app_colour_primary = '';
        $app_colour_pin = '';
        $app_colour_star = '';
        $app_options = array();

        foreach ($design_tasks as $design_task) {

            switch ($design_task["AppTypeDesignTask"]["DesignTask"]["ContributionType"]["type"]) {
                
                case 'name':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'name')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_name = $contribution_data->name;
                    }       
                    break;

                case 'logo':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'logo')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_logo = $contribution_data->url;
                    }
                    break;

                case 'colour':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'colour')
                        ));

                    if (count($contribution) > 0) {
                        $contribution_data = json_decode($contribution["Contribution"]["data"]);
                        $app_colour_primary = $contribution_data->primary;
                        $app_colour_pin = $contribution_data->pin;
                        $app_colour_star = $contribution_data->star;
                    }
                    break;

                case 'options':
                    $contributions = $this->Contribution->find('all', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'options'),
                        'limit' => 4
                        ));

                    if (count($contributions) >= 4) {

                        $index = 0;

                        foreach ($contributions as $contribution) {

                            $contribution_data = json_decode($contribution["Contribution"]["data"]);

                            $app_options[$index] = ucfirst($contribution_data->name);

                            $index++;
                        }
                    }
                    break;

                case 'marker':
                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.movement_design_task_id' => $design_task["MovementDesignTask"]["id"]),
                        'order' => 'Contribution.votes DESC',
                        'contain' => array('ContributionType.type' => 'marker')
                        ));

                    $contribution_data = json_decode($contribution["Contribution"]["data"]);
                    $app_pin_style = $contribution_data->identifier;
                    break;
            }

        }

        $app_configuration = array("OptionA" => $app_options[0], "OptionB" => $app_options[1], "OptionC" => $app_options[2], "OptionD" => $app_options[3], "PrimaryColor" => $app_colour_primary, "PinColor" => $app_colour_pin, "StarColor" => $app_colour_star, "PinStyle" => $app_pin_style);

        // Create a new published app

        $this->PublishedApp->create();
        
        $data = array(
            'PublishedApp' => array(
                'id' => $movement["Movement"]["id"],
                'app_type_id' => 1,
                'creator_id' => $movement["Movement"]["user_id"],
                'slug' => strtolower(Inflector::slug($app_name, '-')),
                'store_listing_name' => $app_name,
                // 'store_listing_description'
                'app_name' => $app_name,
                // 'app_icon'
                'app_identifier' => $movement['Movement']['identifier'],
                'movement_id' => $movement["Movement"]["id"],
                // 'ga_identifier'
                'movement_url' => 'https://app-movement.com/view/' . $movement["Movement"]["id"],
                // 'ios_min_supported_version'
                // 'android_min_supported_version'
                // 'ios_latest_version'
                // 'android_latest_version'
                // 'api_current_version'
                // 'api_key'
                // 'global_salt'
                'app_base_url' => 'https://app-movement.com/api/geolocation/' . $movement['Movement']['identifier'],
                'auth_base_url' => 'https://app-movement.com/api/auth',
                'app_configuration' => json_encode($app_configuration),
                // 'ios_download_link'
                // 'android_download_link'
                // 'published'
                // 'foursquare_base_url'
                // 'foursquare_client_id'
                // 'foursquare_client_secret'
                // 'foursquare_version'
                // 'share_hashtags'
                )
            );

        $this->PublishedApp->save($data);

        $response['meta']['success'] = true;
        
        // Send response
        return $this->PublishedApp;
    }
}
?>
