<?php

class Client extends AppModel {
	var $name = 'Cleint';
	var $useTable = false;

			function _client_domains(){ // list of all client domains
			$clients[] = 
				 array(
					'client_name' => 'MLS TEST 1',
					'id' => 'mlstest1',
					'alias' => array(
						'mls-test1.nu-designs.us',
						'mls-test1.com',
						'www.mls-test1.com',
					),
					'theme' => 'lisacapurro',
					'search_conditions' => array(
						'all' => $this->_all(),
						'my_all' => array(
							'name' => 'My Listings: All Counties',
							'conditions' => array(
								'Property.Agent_NRDS_ID' => 'B4708',
							),
						),
						'my_sonoma' => array(
							'name' => 'My Listings: Sonoma County (Residential)',
							'conditions' => array(
								'Property.Agent_NRDS_ID' => 'B4708',
								'Property.County' => 'Sonoma',
								'Property.Property_Type' => 'RESI',
							),							
						),
					),
				);
			$clients[] = 			
			 	array(
					'client_name' => 'MLS TEST 2',
					'id' => 'mlstest2',
					'alias' => array(
						'mls.nu-designs.us',
						'mls-test2.nu-designs.us',
						'mls-test2.com',
						'www.mls-test2.com',
					),
					'theme' => 'garland',
					'search_conditions' => array(
					    'all' => $this->_all(),
						'my_all' => array(
							'name' => 'My Listings: All Counties',
							'conditions' => array(
								 'Property.Office_ID' => 'AALLN21',
							),
						),
						'my_mendo' => array(
							'name' => 'My Listings: Mendocino County (Residential)',
							'conditions' => array(
								 'Property.Office_ID' => 'AALLN21',
								'Property.County' => 'Sonoma',
								'Property.Property_Type' => 'RESI',
							),							
						),
					),
				);	
			return $clients;		
		}
		
	function _all(){
		$conditions['name'] = 'All Listings';
		$conditions['conditions'] = null;
		return $conditions;
	}
	
	
}