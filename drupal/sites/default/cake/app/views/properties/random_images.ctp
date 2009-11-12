<?
/*
	
	$output = null;
	echo '<div id="ghdetailimgs">';
	foreach($images as $image){
	 	$image = $html->link($html->image(array('controller' => 'properties', 'action' => 'thumbnail', base64_encode($image['Property']['Primary_Picture_Url']).'/size:full/height:200/width:200'), array('rel' => 'thickbox')), array('controller' => 'properties', 'action' => 'view', $image['Property']['ML_Number_Display']), null, null, false ); 
		echo $image.'<br /><br />';
	}

	echo '<br >';
	echo '</div>';
*/
echo $this->element('random_images', array('images' => $images));
?>