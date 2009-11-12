<?php
	if(isset($search_list)){		
		$output = array();
		foreach($search_list as $value){
			if($value != 'City: ---'){
				$output[] = $value;
			}
		}
		echo '<br />';
		echo '<b>'.$text->toList($output).'</b> ';
		echo $html->link('New Search', array('controller' => 'listings', 'action' => 'search'));
/*
		echo ' | ';
		echo $html->link('Refine Search', array('controller' => 'listings', 'action' => 'search', 'filter', 'option:advanced'));
*/
		echo '<br />';
	}
?>