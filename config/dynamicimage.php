<?php defined('SYSPATH') or die('No direct script access.');
$config['compression'] = array						// Compression quality
(
	IMAGETYPE_PNG 	=> 0,									// 0 highest quality, 9 lowest quality (use 0 for almost all circumstances)
	IMAGETYPE_JPEG	=> 100,									// 100 highest quality, 0 lowest quality (70 - 80 is best)
	IMAGETYPE_GIF	=> FALSE								// No compression for gif
);

$config['width']					= FALSE;			// Default output width
$config['height']					= FALSE;			// Default output height
$config['maintain_ratio']			= 'height';		// Ratio maintain, width or height

$config['base_directory']			= DOCROOT;		// directory to append to all GET file vars
$config['cache_dir']				= realpath('img');
