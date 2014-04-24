<?php
/*
Version: 1.0
Plugin Name: TwitterCards
Author: umrysh
Description: Twitter Cards
*/

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
 
class TwitterCard
{
  function twittercard_load ($content)
  {
    global $template,$picture,$page;
    
    // get image url
    $query = sprintf('
		  select path, name
		  FROM '.IMAGES_TABLE.'
		  WHERE id = %s
		;',
		$page['image_id']);
	$result = pwg_query($query);
	$row = pwg_db_fetch_assoc($result);
	$url = substr($row['path'],2);
    $title= str_replace('"', '\"',$row['name']);

    $extension_pos = strrpos($url, '.');
    $thumb = substr($url, 0, $extension_pos) . '_tw_thumb' . substr($url, $extension_pos);
    // Check if a thumb already exists, otherwise create a thumb
    if (!file_exists( $thumb ))
    {
        /**
        * Create a thumbnail image from $inputFileName no taller or wider than 
        * $maxSize. Returns the new image resource or false on error.
        * Author: mthorn.net
        */
        function thumbnail($inputFileName, $maxSize = 100)
        {
            $info = getimagesize($inputFileName);

            $type = isset($info['type']) ? $info['type'] : $info[2];

            // Check support of file type
            if ( !(imagetypes() & $type) )
            {
                // Server does not support file type
                return false;
            }

            $width  = isset($info['width'])  ? $info['width']  : $info[0];
            $height = isset($info['height']) ? $info['height'] : $info[1];

            // Calculate aspect ratio
            $wRatio = $maxSize / $width;
            $hRatio = $maxSize / $height;

            // Using imagecreatefromstring will automatically detect the file type
            $sourceImage = imagecreatefromstring(file_get_contents($inputFileName));

            // Calculate a proportional width and height no larger than the max size.
            if ( ($width <= $maxSize) && ($height <= $maxSize) )
            {
                // Input is smaller than thumbnail, do nothing
                return $sourceImage;
            }
            elseif ( ($wRatio * $height) < $maxSize )
            {
                // Image is horizontal
                $tHeight = ceil($wRatio * $height);
                $tWidth  = $maxSize;
            }
            else
            {
                // Image is vertical
                $tWidth  = ceil($hRatio * $width);
                $tHeight = $maxSize;
            }

            $thumb = imagecreatetruecolor($tWidth, $tHeight);

            if ( $sourceImage === false )
            {
                // Could not load image
                return false;
            }

            // Copy resampled makes a smooth thumbnail
            imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
            imagedestroy($sourceImage);

            return $thumb;
        }

        /**
         * Save the image to a file. Type is determined from the extension.
         * $quality is only used for jpegs.
         * Author: mthorn.net
         */
        function imageToFile($im, $fileName, $quality = 80)
        {
            if ( !$im || file_exists($fileName) )
            {
               return false;
            }

            $ext = strtolower(substr($fileName, strrpos($fileName, '.')));

            switch ( $ext )
            {
                case '.gif':
                    imagegif($im, $fileName);
                    break;
                case '.jpg':
                case '.jpeg':
                    imagejpeg($im, $fileName, $quality);
                    break;
                case '.png':
                    imagepng($im, $fileName);
                    break;
                case '.bmp':
                    imagewbmp($im, $fileName);
                    break;
                default:
                    return false;
            }

            return true;
        }

        $im = thumbnail($url, 435);
        imageToFile($im, $thumb);
    }

    $info = getimagesize( $thumb );
    $width  = isset($info['width'])  ? $info['width']  : $info[0];
    $height = isset($info['height']) ? $info['height'] : $info[1];
    

    $template->append('head_elements',
    '<meta name="twitter:card" content="photo"><meta name="twitter:title" content="' . $title . '"><meta name="twitter:image" content="http://' . $_SERVER['HTTP_HOST'] . '/' . $thumb . '"><meta name="twitter:image:width" content="' . $width . '"><meta name="twitter:image:height" content="' . $height . '"><meta property="og:title" content="' . $title . '" /><meta property="og:image" content="http://' . $_SERVER['HTTP_HOST'] . '/' . $thumb . '" />');
  }
}

$obj = new Twittercard();
add_event_handler('render_element_content', array(&$obj, 'twittercard_load'));

?>