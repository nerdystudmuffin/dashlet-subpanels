<?php

function smarty_function_sugar_link($params, &$smarty)
{
	if(empty($params['module'])){
		$smarty->trigger_error("sugar_link: missing 'module' parameter");
		return;
	}
	$action = (!empty($params['action']))?$params['action']:'index';
    
    $link_url = 'index.php?';
    $link_url .= 'module='.$params['module'].'&action='.$action;

    if (!empty($params['record'])) { $link_url .= "&record=".$params['record']; }
    if (!empty($params['extraparams'])) { $link_url .= '&'.$params['extraparams']; }

    if (isset($params['link_only']) && $params['link_only'] == 1 ) {
        // Let them just get the url, they want to put it someplace
        return $link_url;
    }

	$id = (!empty($params['id']))?' id="'.$params['id'].'" ':'';
	$class = (!empty($params['class']))?' class="'.$params['class'].'" ':'';
	$style = (!empty($params['style']))?' style="'.$params['style'].'" ':'';
	$title = (!empty($params['title']))?' title="'.$params['title'].'" ':'';
	$accesskey = (!empty($params['accesskey']))?' accesskey="'.$params['accesskey'].'" ':'';
    $options = (!empty($params['options']))?' '.$params['options'].' ':'';
    $label = (!empty($params['label']))?$params['label']:$GLOBALS['app_list_strings']['moduleList'][$params['module']];

    $link = '<a href="'.$link_url.'" '.$id.$class.$style.$options.'>'.$label.'</a>';
    return $link;
}
