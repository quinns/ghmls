<?php
class ListingImage extends AppModel {

	var $name = 'ListingImage';
	var $primaryKey = 'mls_number';
	//var $useTable = 'mls_images';
	var $useTable = false;
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasOne = array(
			'Listing' => array('className' => 'Listing',
								'foreignKey' => 'mls_number',
								'fields' => '',
								'order' => '',
								'conditions' => '',
			)
	);
	/*
	function listing_image($id){
		$this->autoRender = false;
		$this->ListingImage->recursive = -1;
		$listingImage = $this->ListingImage->findById($id);
		$im = imagecreatefromjpeg($listingImage['ListingImage']['local_path']);
		header('Content-Type: image/jpeg');
		imagejpeg($im);
		imagedestroy($im);
	}
	*/
}
?>