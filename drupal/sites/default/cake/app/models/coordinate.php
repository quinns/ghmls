<?php
class Coordinate extends AppModel {

	var $name = 'Coordinate';
	var $useTable = 'mls_coordinates';
	var $primary_key = 'ML_Number_Display';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Listing' => array('className' => 'Listing',
								'foreignKey' => 'ML_Number_Display',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);

}
?>