<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 * $Id$
 *
 * @package    DynamicImage
 * @author     Polaris Digital
 * @copyright  (c) 2008 Polaris Digital
 * @license    GNU Public Licence v3
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
				'maintain_ratio' 		=> isset($get['mr']) ? $get['mr'] : FALSE,
				'format'				=> isset($get['format']) ? $get['format'] : FALSE,
			);
			
			$hash = md5(implode("", $image_settings));
			$cacheDirectory = Kohana::config('dynamicimage.cache_dir');
			$fileExtension = strtolower(pathinfo($get['file'], PATHINFO_EXTENSION));
			$cacheFile = $cacheDirectory.'/'.$hash.'.'.$fileExtension;

			if(!file_exists($cacheFile)) {
				$image_settings += Kohana::config('dynamicimage');

				$filename = $image_settings['base_directory'].$image_settings['filename'];

				if(is_file($filename)) {
					$image = new Image($filename);

					if($image_settings['maintain_ratio'] == 'height') {
						$maintain_ratio = Image::HEIGHT;
					} else if($image_settings['maintain_ratio'] == 'auto') {
						$maintain_ratio = Image::AUTO;
					} else {
						$maintain_ratio = Image::WIDTH;
					}

					if(!$image_settings['width']) $image_settings['width'] = $image->width;
					if(!$image_settings['height']) $image_settings['height'] = $image->height;

					$image->resize($image_settings['width'], $image_settings['height'], $maintain_ratio)->quality($image_settings['compression'][$image->type]);
					
					$image->save($cacheFile);
				} else {
					return FALSE;
				}
			}
			
			if($fileExtension == "jpg")
				$fileExtension = "jpeg";
			
			header("Cache-Control: max-age=604800, public"); // two weeks
			header('Content-Type: image/'.$fileExtension);
			readfile($cacheFile);
		} else {
			throw new Kohana_Exception('An image file in GIF, JPG or PNG format is required');
		}
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