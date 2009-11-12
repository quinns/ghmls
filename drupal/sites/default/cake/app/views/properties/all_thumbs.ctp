<?php

foreach($thumbs as $thumb){
	echo $html->image(array('controller' => 'properties', 'action' => 'thumbnail',  base64_encode($thumb['Property']['Primary_Picture_Url'])));
}
?>