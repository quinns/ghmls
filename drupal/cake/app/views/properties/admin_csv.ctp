<?php
	$fields = $properties[0]['Property'];
	$fields = array_keys($fields);
	echo $text->toList($fields, ',')."\r\n";
	foreach($properties as $property){
		foreach($property['Property'] as $value){
			echo "'".addslashes($value)."',";
		}
		echo "\r\n";
	}
