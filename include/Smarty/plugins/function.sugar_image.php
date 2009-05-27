<?php

function smarty_function_sugar_image($params, &$smarty)
{
	
	if(!isset($params['name'])){
		$smarty->trigger_error("sugar_field: missing 'name' parameter");
		return;
	}
	$height = (!empty($params['height']))?$params['height']:'48';
	$width = (!empty($params['width']))?$params['width']:'48';
	$image = (!empty($params['image']))?$params['image']:$params['name'];
	$altimage = (!empty($params['altimage']))?$params['altimage']:$params['name'];
	return getStudioIcon($image, $altimage, $height, $width);
	
}
