<?php
class City extends AppModel {

	var $name = 'City';
	//var $primaryKey = 'mls_number';
	var $useTable = 'mls_cities';
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty'
		)
	);
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'County' => array('className' => 'County',
								'foreignKey' => 'county_id',
								'dependent' => false,
								'conditions' =>  '',
								'fields' => '',
								'order' => '',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			)
	);
	
	
	/*
	function counties(){
		$this->autoRender = false;
		$this->recursive = -1;
		$counties = $this->find('all', array('fields' => 'DISTINCT Listing.county', 'order' => 'Listing.county ASC'));
		$county_list = Set::combine($counties, '{n}.Listing.county', '{n}.Listing.county');
		$this->set('counties', $county_list);
	}
	*/
}
?>