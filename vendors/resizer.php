<?php
// Set the php memory limit
ini_set('memory_limit', '512M');
 
class ResizeImage {
 
   var $image;
   var $image_type;
 
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_PNG, $compression=100, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }

      imagepng($this->image,$filename);
   }
   function output($image_type=IMAGETYPE_PNG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
    
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getHeight() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToSize($size) {
      if ($this->getWidth() > $this->getHeight()) {
         $this->resizeToHeight($size);
      } else {
         $this->resizeToWidth($size);
      }
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
   function makeThumb($size) {
      $new_image = imagecreatetruecolor($size, $size);
      $x = 0;
      $y = 0;
      if ($this->getWidth() < $this->getHeight()) {
         $smallestSide = $this->getWidth();
         $y = ($this->getHeight() - $this->getWidth()) / 2;
      } else {
         $smallestSide = $this->getHeight();
         $x = ($this->getWidth() - $this->getHeight()) / 2;
      }

      imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $size, $size, $smallestSide, $smallestSide);
      $this->image = $new_image;
   }
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);

      imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
      imagealphablending($new_image, false);
      imagesavealpha($new_image, true);

      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
    
      $this->image = $new_image;
   }     
 
}
?>