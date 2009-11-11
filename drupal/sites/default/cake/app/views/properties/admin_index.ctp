<?php

	echo '<h1>'.$this->pageTitle.'</h1>';
	foreach($properties as $property){
	foreach($property['Property'] as $key => $value){
		$value = trim($value);
		if(!empty($value)){
			$output[$key] = $value;
		}
	}
	debug($output);	
	}
