<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

define('SPRITE_FOLDER', 'sprite/');
define('PATH_SPRITE', PATH_RESOURCE_BUNDLE . SPRITE_FOLDER);
 
/**
 * Can generate an image sprite and the associated css file.
 * @author David Reignier
 *
 */
class ImageSpriteUtil extends Util {
	
	/**
	 * Used to keep files.
	 * @var array[string]
	 */
	private $files = array();
	
	/**
	 * Files height.
	 * @var array[int]
	 */
	private $filesHeights = array();
	
	/**
	 * Heights of sprites indexed by width.
	 * @var array[int] 
	 */
	private $sizes = array();
	
	/**
	 * Images of sprites
	 * @var resource
	 */
	private $images = array();
	
	/**
	 * Extensions handled by the util and the functions used to create the images.
	 */
	private $extensions = array(
		'gif' => 'imagecreatefromgif',
		'png' => 'imagecreatefrompng',
		'jpeg' => 'imagecreatefromjpeg',
		'jpg' =>  'imagecreatefromjpeg'
	);
	
	/**
	 * Get the css class for the tile
	 * @param $file string
	 * 	A file.
	 * @return string
	 * 	The css class for the given file
	 */
	private function getCssClass($file) {
		$file = substr($file, 0, -4);
		$file = substr($file, strlen(PATH_RESOURCE));
		$file = preg_replace('#[/\.]#', '_', $file);
		return $file;
	}
	
	/**
	 * Generate a sprite. Generate a css file and some images (in production mode).
	 * @param $sprite string
	 * 	The name of the sprite. With this name, the sprite configuration can be found.
	 */
	public function sprite($sprite) {
		util('File')->checkDirectory(PATH_SPRITE);
		$config = config('sprite');
      $pattern = PATH_RESOURCE . $config[$sprite];
		if (isDevMode()) {
			$this->devSprite($sprite, $pattern);
		} else {
			$this->prodSprite($sprite, $pattern);
		}
	}
	
	/**
	 * Generate a sprite in dev mode.
	 * @param $sprite string
	 * 	The name of the sprite.
	 * @param $pattern
	 * 	The pattern for the sprite.
	 */
	private function devSprite($sprite, $pattern) {
		$css = '';
		
		// Find files an generate CSS
		foreach (util('File')->glob($pattern) as $file) {
			if (array_key_exists(substr($file, -3), $this->extensions)) {
				list($x, $y) = getimagesize($file);
				$css .= '.' . $this->getCssClass($file) . " {\n";
				$css .= "\tbackground-image: url('" . config('url.root') . $file . "');\n";
				$css .= "\theight: $y" . "px;\n";
				$css .= "\twidth: $x" . "px;\n";
				$css .= "}\n\n";
			}
		}
		
		$sprite = PATH_RESOURCE_BUNDLE . $sprite;
      util('File')->checkDirectory($sprite);
      file_put_contents($sprite, $css);
	}
	
	/**
	 * Generate a sprite in production mode.
	 * @param $sprite string
	 * 	The name of the sprite.
	 * @param $pattern
	 * 	The pattern for the sprite.
	 */
   private function prodSprite($sprite, $pattern) {
      $css = '';

		// Find files
      foreach (util('File')->glob($pattern) as $file) {
			if (array_key_exists(substr($file, -3), $this->extensions)) {
	      	list($x, $y) = getimagesize($file);
				
				if (!isset($this->files["$x"])) {
					$this->files[$x] = array();
					$this->sizes[$x] = 0;
				}
				
				$this->files[$x][] = $file;
				$this->sizes[$x] += $y;
				$this->filesHeights[$file] = $y;
			}
      }
		
		// Create and initialize images
		foreach ($this->sizes as $x => $y) {
			$this->images[$x] = imagecreatetruecolor($x, $y);
			
			// Add alpha channel
			imagesavealpha($this->images[$x], true);
			$alpha = imagecolorallocatealpha($this->images[$x], 0, 0, 0, 127);
			imagefill($this->images[$x], 0, 0, $alpha);
		}
		
		// Gather images
		foreach ($this->files as $x => $files) {
			$y = 0;
			$spritePath = PATH_SPRITE . "sprite_$x.png";
			foreach ($files as $file) {
				$height = $this->filesHeights[$file];
				$image = call_user_func($this->extensions[substr($file, -3)], $file);
				imagecopy($this->images[$x], $image, 0, $y, 0, 0, $x, $height);
				
				$css .= '.' . $this->getCssClass($file) . " {\n";
				$css .= "\tbackground-image: url('" . config('url.root') . $spritePath . "');\n";
				$css .= "\tbackground-position: 0 -$y" . "px;\n";
				$css .= "\theight: $height" . "px;\n";
				$css .= "\twidth: $x" . "px;\n";
				$css .= "}\n\n";
				
				$y += $height;
			}
			imagepng($this->images[$x], $spritePath);
		}
      
      $sprite = PATH_RESOURCE_BUNDLE . $sprite;
      util('File')->checkDirectory($sprite);
      file_put_contents($sprite, $css);
   }
   
   /**
    * Create an url for a sprite file
    * @param $file string
    *    The file name.
    * @return string
    *    The url for the sprite file.
    */
   public function url($file) {
      return randomParamUrl(siteUrl("image_sprite/$file"));
   }
}  

/**
 * @see ImageSpriteUtil#url
 */
function imageSpriteUrl($file) {
   return util('ImageSprite')->url($file);
}
