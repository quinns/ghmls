<?
/* 	Set up variables */
	$is_valid = null;
	$div = 'ghrandomdetailimgs';
	$max_height = 200;
	$max_width = 200;
	$output = null;
	$limit = 4;
	$count = 0;
	$valid = 0;
	$image_counter = 1;
	echo '<div id="'.$div.'">';
	foreach($images as $image){
		
		if($count < $limit){
		
			$image_attributes = $image;
			$my_image =  array('controller' => 'properties', 'action' => 'thumbnail', base64_encode($image['Property']['Primary_Picture_Url']).'/size:full/height:'.$max_height.'/width:'.$max_width.'/image:random_'.($image_counter++).'.jpg');		
			$attr = getimagesize('http://'.$_SERVER['SERVER_NAME'].'/mls/properties/thumbnail/'.$my_image[0]);
		 	$image = $html->link($html->image($my_image, array('rel' => 'thickbox', 'title' => strip_tags($image['Property']['Marketing_Remarks']))), array('controller' => 'properties', 'action' => 'view', $image['Property']['ML_Number_Display']), array('class' => null), null, false ); 
/*
		 	debug($attr);
*/
		 	if(($attr[0] <= $max_width) && $attr[1] <= $max_height-50){
		 		$is_valid = true;
				echo $image.'<br />';
				echo $html->link($image_attributes['Property']['City'].' ('.$image_attributes['Property']['County'].')', array('controller' => 'properties', 'action' => 'view', $image_attributes['Property']['ML_Number_Display']));
				echo '<br /><br />';
				$valid ++;
				if($valid >= $limit){
					$count = $limit;
				} else {
					$is_valid = false;				
				}
			}
		
	}
/*
		debug($is_valid);
*/
		if($is_valid == true){
			$count++;		
		}
	}
	
/*
	debug($limit);
	debug($count);
	debug($valid);
*/
	echo '</div>';
?>