<?php
class Property extends AppModel {

	var $name = 'Property';
	// var $primaryKey = 'mls_number';
	// var $useTable = 'mls_listings';
	var $primaryKey = 'ML_Number_Display';
	//var $useTable = 'mls_properties';
	//var $useTable = 'mls_combined_properties';
	var $useTable = 'mls_all_fields';
	
	
	var $belongsTo = array(
			'PropertyType' => array('className' => 'PropertyType',
								'foreignKey' => 'Property_Type',
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
	
	//var $cacheQueries;

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	/*
	var $hasMany = array(
			'ListingImage' => array('className' => 'ListingImage',
								'foreignKey' => 'mls_number',
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

	*/

/*
	var $hasOne = array(
			'Coordinate' => array('className' => 'Coordinate',
								'foreignKey' => 'ML_Number_Display',
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
*/


	
	function beforeFind(){
		//die(debug($this->schema()));
/*
		$this->_schema['bedrooms']['type']  ='float';
		$this->_schema['bathrooms']['type']  ='float';
*/


		$this->_schema['Search_Price_Display']['type']  ='float';
		$this->_schema['Square_Footage']['type']  ='float';
		return true;
	}
	function counties(){
		$this->autoRender = false;
		$this->recursive = -1;
		$counties = $this->find('all', array('fields' => 'DISTINCT Property.County', 'order' => 'Property.County ASC'));
		$county_list = Set::combine($counties, '{n}.Property.County', '{n}.Property.County');
		$this->set('counties', $county_list);
	}

}
?>