<h2><?php echo $this->pageTitle; ?></h2>

<?




	echo '<p>'.$number->format(count($agents)).' agent(s) were found matching your search criteria.</p>';

	

	foreach($agents as $agent) {
		echo '<b>'.$html->link($agent['Listing']['Agent_Name'], array('controller' => 'listings', 'action' => 'listing_agent', $agent['Listing']['Agent_Number'])).'</b><br />';
		if (!empty($agent['Listing']['Agent_Phone_1'])) { echo 'Phone: '.$agent['Listing']['Agent_Phone_1'].'<br />'; }
		if (!empty($agent['Listing']['Agent_Email_Address'])) { echo 'Email: '.$text->autoLink($agent['Listing']['Agent_Email_Address']).'<br />'; }
		if (!empty($agent['Listing']['Agent_Web_Page_Address']) && trim($agent['Listing']['Agent_Web_Page_Address']) != '') { echo 'Website: '.$text->autoLink($agent['Listing']['Agent_Web_Page_Address']).'<br />'; }
		if (!empty($agent['Listing']['Office_Long_Name'])) { echo 'Office: ' .$html->link($agent['Listing']['Office_Long_Name'], array('controller' => 'listings', 'action' => 'listing_office', $agent['Listing']['Office_Broker_ID'])); }
		echo '<hr />';
	}

 ?>
 
