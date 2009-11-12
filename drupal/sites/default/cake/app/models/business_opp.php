<?php
class BusinessOpp extends AppModel {

	var $name = 'BusinessOpp';
	var $primaryKey = 'ML_Number_Display';
	var $useTable = 'mls_bus_opps';
/*
	
	function beforeFind(){
		return true;
	}
	
	
	function counties(){
		$this->autoRender = false;
		$this->recursive = -1;
		$counties = $this->find('all', array('fields' => 'DISTINCT BusinessOpp.County', 'order' => 'BusinessOpp.County ASC'));
		$county_list = Set::combine($counties, '{n}.BusinessOpp.County', '{n}.BusinessOpp.County');
		$this->set('counties', $county_list);
	}
*/

}
?>