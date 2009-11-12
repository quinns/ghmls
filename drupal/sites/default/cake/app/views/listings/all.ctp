<?php 
echo '<h2>All regions indexed</h2>';
foreach($output as $key => $value){
	$counties[$key] = $key;
}
echo '<ul>';
foreach($counties as $value){
	echo '<li>'.$html->link($value, array('controller' => 'listings', 'action' => 'region', Inflector::slug(strtolower($value)))).'</li>';
	echo '<ul>';
	foreach($output[$value] as $city){
			echo '<li>'.$html->link($city, array('controller' => 'listings', 'action' => 'city', Inflector::slug(strtolower($city)))).'</li>';
	}
	echo '</ul>';
}
echo '</ul>';
?>