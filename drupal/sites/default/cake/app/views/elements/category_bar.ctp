<?php
if(isset($cat_list)){
	$categories = $params['categories'];
	$property_type = $params['property_type'];
	$region = $params['region'];
	$controller = $this->params['controller'];
	$action = $this->params['action'];
	$has_other =  null;
	foreach($categories as $key => $category){
		if($category['PropertyType']['name'] == $property_type['PropertyType']['name']){
		//	$cat_list[] = $category['PropertyType']['name'];
		$has_other = true;
		} else if ($category['PropertyType']['count'] > 0) {
			$cat_list[] = $html->link($category['PropertyType']['name'], array('controller' => $controller, 'action' => $action, $this->params['pass'][0].'/'.$category['PropertyType']['slug'])).' ('.$number->format($category['PropertyType']['count']).')';
		}
	}
	
	if($has_other == true){
		echo 'Other categories ';
	} else{
		echo 'Categories ';
	}
	echo ' in <b>'.$params['area'].'</b>: '.$text->toList($cat_list);
	/* debug($cat_list); */
	/* echo $text->toList($categories); */
}
?>