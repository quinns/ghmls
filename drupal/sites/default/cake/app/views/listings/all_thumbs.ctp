<?php

foreach($thumbs as $thumb){
	echo $html->image(array('controller' => 'listings', 'action' => 'thumbnail',  base64_encode($thumb['Listing']['Primary_Picture_Url'])));
}
?>