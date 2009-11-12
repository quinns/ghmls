<h2>Welcome to the GreatHomes.org development site</h2>
<p>Please choose the county or region you wish to browse, or choose <?php echo $html->link('All Regions', array('controller' => 'listings', 'action' => 'index')); ?>: </p>

<?php
	/*
	echo '<h2>Select-menu style navigation:</h2>';
	echo $form->create(array('type' => 'GET', 'action' => 'region'));
	echo $form->select('Listing.region', $counties, null,  null, false);
	echo $form->end('Find Homes');
	echo '<h2>Text-list style navigation:</h2>';
	*/
	echo '<ul>';
	foreach($counties as $county){
		echo '<li>'.$html->link($county, array('controller' => 'listings', 'action' => 'region', urlencode(strtolower($county)))).'</li>';
	}
	echo '</ul>';
	echo "\r\r\r";
?>