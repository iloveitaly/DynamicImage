<?php defined('SYSPATH') or die('No direct script access.');

/*
 * @author		Michael Bianco
 * 
 * Original idea taken from http://code.google.com/p/kdynamicimage/ although mostly all of the code was rewritten
 */

class DynamicImage_Controller extends Controller {
	public function index() {
		if($this->input->get('file') || $this->input->get('f')) {
			$get = $this->input->get();
			
			// alias some options
			foreach(array('w' => 'width', 'h' => 'height', 'f' => 'file') as $key => $newKey) {
				if(!empty($get[$key])) $get[$newKey] = $get[$key];
			}
			
			// just merge the GET with $confg... duh!
			$image_settings = array(
				'filename' 				=> $get['file'],
				'width'	  				=> isset($get['width']) ? $get['width'] : FALSE,
				'height'   				=> isset($get['height']) ? $get['height'] : FALSE,
				'maintain_ratio' 		=> isset($get['mr']) ? $get['mr'] : FALSE
			);
			
			$cacheFile = self::generate_cache($image_settings);
			
			if($cacheFile) {
				$fileExtension = pathinfo($image_settings['filename'], PATHINFO_EXTENSION);

				if($fileExtension == "jpg")
					$fileExtension = "jpeg";

				header("Cache-Control: max-age=604800, public"); // two weeks
				header('Content-Type: image/'.$fileExtension);
				readfile($cacheFile);
			}
		} else {
			Kohana::log('error', 'An image file in GIF, JPG or PNG format is required');
		}
	}
	
	public static function generate_cache($image_settings) {
		if(!empty($image_settings['src']))
			$image_settings['filename'] = $image_settings['src'];
		
		$hash = md5(implode("", $image_settings));
		$cacheDirectory = Kohana::config('dynamicimage.cache_dir');
		$fileExtension = strtolower(pathinfo($image_settings['filename'], PATHINFO_EXTENSION));
		$cacheFile = $cacheDirectory.'/'.$hash.'.'.$fileExtension;

		if(!file_exists($cacheFile)) {
			$image_settings += Kohana::config('dynamicimage');
			$fileName = $image_settings['base_directory'].$image_settings['filename'];

			if(is_file($fileName)) {
				self::process_image($image_settings, $fileName)->save($cacheFile);
			} else {
				Kohana::log('error', 'Invalid file specified');
				return FALSE;
			}
		}
		
		return $cacheFile;
	}
	
	public static function process_image($image_settings, $filePath) {
		/*
		$image_settings:
			width, height
			maintain_ratio
		*/
		
		$image = new Image($filePath);

		if($image_settings['maintain_ratio'] == 'height') {
			$maintain_ratio = Image::HEIGHT;
		} else if($image_settings['maintain_ratio'] == 'auto') {
			$maintain_ratio = Image::AUTO;
		} else {
			$maintain_ratio = Image::WIDTH;
		}
		
		if(empty($image_settings['width'])) $image_settings['width'] = $image->width;
		if(empty($image_settings['height'])) $image_settings['height'] = $image->height;

		return $image->resize($image_settings['width'], $image_settings['height'], $maintain_ratio)->quality($image_settings['compression'][$image->type]);
		
		return $image;
	}
	
	public function clear_cache() {
		$targetDir = Kohana::config('dynamicimage.cache_dir');
		$files = listdir($targetDir);

		foreach($files as $file) {
			if(!unlink($targetDir.'/'.$file)) {
				Kohana::log('error', 'Error deleting file '.$targetDir.$file);
			}
		}
	}
}