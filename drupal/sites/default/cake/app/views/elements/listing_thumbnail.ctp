<?php
	$this->params['prototype'] = true;
	//$this->params['jquery'] = true;
	echo $html->link($html->image(array('controller' => 'listing_images', 'action' => 'listing_thumbnail', 'admin' => 0, $listingImage['ListingImage']['id'])), array('admin' => 0, 'controller' => 'listing_images', 'action' => 'listing_image', $listingImage['ListingImage']['id']), array('rel' => 'lightbox[group]'), null, false);
	echo "\r\n";
?>