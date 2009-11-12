<h2><?php echo $this->pageTitle; ?></h2>
<?php
	echo '<p>'.$number->format(count($agents)).' agent(s) were found matching your search criteria.</p>';
	foreach($agents as $agent) {
		echo '<b>'.$html->link($agent['Property']['Agent_Name'], array('controller' => 'properties', 'action' => 'listing_agent', $agent['Property']['Agent_Number'])).'</b><br />';
		if (!empty($agent['Property']['Agent_Phone_1'])) { echo 'Phone: '.$agent['Property']['Agent_Phone_1'].'<br />'; }
		if (!empty($agent['Property']['Agent_Email_Address'])) { echo 'Email: '.$text->autoLink($agent['Property']['Agent_Email_Address']).'<br />'; }
		if (!empty($agent['Property']['Agent_Web_Page_Address']) && trim($agent['Property']['Agent_Web_Page_Address']) != '') { echo 'Website: '.$text->autoLink($agent['Property']['Agent_Web_Page_Address']).'<br />'; }
		if (!empty($agent['Property']['Office_Long_Name'])) { echo 'Office: ' .$html->link($agent['Property']['Office_Long_Name'], array('controller' => 'properties', 'action' => 'listing_office', $agent['Property']['Office_Broker_ID'])); }
		echo '<hr />';
	}

?>