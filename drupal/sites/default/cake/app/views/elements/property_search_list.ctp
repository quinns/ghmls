<?php
	if(isset($search_list)){
		$link_extra = null;
/*
		debug($search_list);
*/
		$output = array();
		foreach($search_list as $value){
			if($value != 'City: ---' && $value != 'City: Array'){
				$output[] = $value;
			}
		}
		echo '<br />';
		echo '<b>'.$text->toList($output).'</b> ';

		

		if(isset($search_options['County'])){
			$link_extra = '/county:'.strtolower(Inflector::slug($search_options['County']['name']));	
		}
		if(isset($search_options['Property']['open_house_search'])){
			$link_extra .= '/open_house:1';
		}
		echo $html->link('New Search', array('controller' => 'properties', 'action' => 'search', $link_extra));
/*
		echo ' | ';
		echo $html->link('Refine Search', array('controller' => 'properties', 'action' => 'search', 'filter', 'option:advanced'));
*/
		echo '<br />';
	}
?>