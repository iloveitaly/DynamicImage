<?
class html extends html_Core {
	public static function dimage($options) {
		if(!isset($options['width'])) $options['width'] = '';
		if(!isset($options['height'])) $options['height'] = '';
		
		$imageURL = 'di/?file='.$options['src']."&width={$options['width']}&height={$options['height']}";
		
		if(isset($options['maintain_ratio'])) {
			$imageURL .= "&mr=".$options['maintain_ratio'];
			unset($options['maintain_ratio']);
		}
		
		$options['src'] = $imageURL;
		
		// strip width / height attributes if they are empty or if no_attributes is specified
		// note that no_attributes does not imply that absolutely no attributes are outputted (alt can still be specified)
		
		if($options['height'] == '' || isset($options['no_attributes'])) unset($options['height']);
		if($options['width'] == '' || isset($options['no_attributes'])) unset($options['width']);
		if(isset($options['no_attributes'])) unset($options['no_attributes']);
		
		return self::image($options, $options);
	}
}
?>