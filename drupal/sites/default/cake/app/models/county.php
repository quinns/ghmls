<?php
class County extends AppModel {

	var $name = 'County';
	//var $primaryKey = 'mls_number';
	var $useTable = 'mls_counties';
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty'
		)
	);
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'City' => array('className' => 'City',
						'foreignKey' => 'county_id',
								'dependent' => false,
								'conditions' =>  'City.status_id = 1',
								'fields' => '',
								'order' => 'City.name ASC',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
						
							//	'finderQuery' => 'SELECT `City`.`id`, `City`.`name`, `City`.`county_id`, `City`.`status_id`, `City`.`created`, `City`.`modified` FROM `cake_mls_cities` AS `City` WHERE `City`.`status_id` = 1 AND `City`.`county_id` = (1) ORDER BY `City`.`name` ASC',
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