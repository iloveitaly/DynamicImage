<?
class html extends html_Core {
	public static function dimage($options) {
		$options = array_merge(array('width' => '', 'height' => '', 'maintain_ratio' => FALSE), $options);
		$options['src'] = DynamicImage_Controller::generate_cache($options);
		
		// strip width / height attributes if they are empty or if no_attributes is specified
		// note that no_attributes does not imply that absolutely no attributes are outputted (alt can still be specified)
		
		unset($options['maintain_ratio']);
		unset($options['height']);
		unset($options['width']);
		
		return Base_Controller::instance()->_generateImageHTML($options);
		
		return self::image($options, $options);
	}
}
?>