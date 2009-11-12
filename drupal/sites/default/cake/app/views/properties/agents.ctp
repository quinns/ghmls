<?php


foreach($agents as $agent){
$params['name'] = $agent['Property']['Agent_Name'];
$params['email'] = $agent['Property']['Agent_Email_Address'];
$serialized_params = urlencode(base64_encode(serialize($params)));

	echo $agent['Property']['Agent_Name'].' '.$html->link($agent['Property']['Agent_Email_Address'], array('controller' => 'properties', 'action' =>
	 'contact', 
	 	'params:'.$serialized_params
	 		 
	 )
	 
	 )
	 .'<br />';
}
/*  debug($agents);  */
 
 ?>