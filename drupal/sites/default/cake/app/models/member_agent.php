<?php
class MemberAgent extends AppModel {

	var $name = 'MemberAgent';
	var $primaryKey = 'Agent_MLS_ID';
	var $useTable = 'mls_agents';
	
	
	var $belongsTo = array(
			'MemberOffice' => array('className' => 'MemberOffice',
								'foreignKey' => 'Office_MLS_ID',
								'dependent' => false,
								'conditions' =>  '',
								'fields' => '',
								'order' => '',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
						
			),
	);
		
		
		var $hasMany = array(
			'Property' => array('className' => 'Property',
								'foreignKey' => 'Agent_NRDS_ID',
								'dependent' => false,
								'conditions' =>  '',


								'fields' => 	'Property.ML_Number_Display, 
												 Property.City,
												 Property.County,
												 Property.Search_Price,
												 Property.Primary_Picture_Url,
												 Property.modified',
										

								'order' => 'Property.Search_Price ASC',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			),
			

			
			
			
	);

	

}
?>