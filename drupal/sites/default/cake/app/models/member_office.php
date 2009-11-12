<?php
class MemberOffice extends AppModel {
	
	var $useTable = 'mls_offices';
	var $name = 'MlsOffice';
	var $primaryKey = 'Office_MLS_ID';


		var $hasMany = array(
			'Property' => array('className' => 'Property',
								'foreignKey' => 'Office_ID',
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
/* 								'finderQuery' => 'SELECT Property.* from mls_all_fields as Property WHERE Property.Office_ID LIKE %{$__cakeID__$}%;', */
								'counterQuery' => ''
			),
			
			'MemberAgent' => array('className' => 'MemberAgent',
								'foreignKey' => 'Office_MLS_ID',
								'dependent' => false,
								'conditions' =>  '',


	
										

								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			),

			
			
			
	);
}
?>