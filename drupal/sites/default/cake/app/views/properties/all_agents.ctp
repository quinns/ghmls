<?php 
	echo '<h2>'.$this->pageTitle.'</h2>';


	echo '<ul>';
	foreach($agents as $agent){
		echo '<li>'.$html->link($agent['Property']['Agent_Name'], array('controller' => 'properties', 'action' => 'listing_agent', $agent['Property']['Agent_Number'])).' ('.$agent['Property']['Office_Long_Name'].')</li>';
	}
	echo '</ul>';
/* debug($agents); */


 ?>