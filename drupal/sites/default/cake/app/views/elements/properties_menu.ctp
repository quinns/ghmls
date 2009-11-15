<?php

echo '<h2>Browse Listings</h2>';
echo '<ul>';
foreach($client_data['search_conditions'] as $key => $value){
	echo '<li>'.$html->link($value['name'], array('controller' => 'properties', 'action' => 'index', '/client:'.$client_data['id'].'/filter:'.$key)).'</li>';
}

echo '</ul>';

/* debug($client_data); */
