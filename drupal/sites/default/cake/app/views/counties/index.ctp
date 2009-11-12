<div class="counties index">
<h2><?php __('Counties');?></h2>
<?	echo $this->element('property_search_bar_simple'); ?>


<ul>

<?

if (isset($this->params['named']['method'])){
	foreach($counties as $key => $value){
		echo '<li>';
		echo $html->link($value, array('controller' => 'properties', 'action' => 'search', 'county:'.Inflector::slug(strtolower($value))));
		echo ' ('.$number->format($summary[$key]).')';
		echo '</li>';
	}

} else {
	foreach($counties as $key => $value){
		echo '<li>';
				echo $html->link($value, array('controller' => 'properties', 'action' => 'search', 'county:'.Inflector::slug(strtolower($value))));

	/* 	echo $html->link($value, array('controller' => 'counties', 'action' => 'view', $key)); */
/* 		echo $html->link($value, array('controller' => 'properties', 'action' => 'region', Inflector::slug(strtolower($value)))); */
		echo ' ('.$number->format($summary[$key]).')';
		echo '</li>';
	}
}
?>

</ul>
</div>
