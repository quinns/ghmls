<?
/*
debug($summary);
*/
?>
<h2><?php echo $this->pageTitle; ?></h2>
<?php 
	echo $this->element('property_search_bar_simple');
	echo '<ul>';
		foreach($types as $value){
			echo '<li>'.$html->link($value['PropertyType']['name'], array('controller' => 'properties','action' => 'index', $value['PropertyType']['slug'])).' ('.$number->format($value['PropertyType']['count']).')</li>';
		}
	echo '</ul>';

