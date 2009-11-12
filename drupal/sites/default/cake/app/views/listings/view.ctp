
<cake:nocache>
<?php

if(isset($_SERVER['HTTP_REFERER'])){
	$referrer =  $_SERVER['HTTP_REFERER'];
	if(stristr($referrer, 'greathomes') !== false){
		echo '<p>'.$html->link('Return to Search Results', $referrer).'</p>';
	}
}

?>
</cake:nocache>


<?php
	//echo '</div>
	echo '<!-- /#ghprevnext -->';
	echo '<h2>'.$this->pageTitle.'</h2>';

	echo '<div id="ghdetailimgs">';		
	
	echo $html->image(array('controller' => 'listings', 'action' => 'image', $listing['Listing']['ML_Number_Display'].'.jpg'), $primary_image['attributes']);
	// start thumbnail output
	if(!empty($listing['Listing']['Virtual_Tour_URL'])){
		echo '<p>'.$html->link('See a "Virtual Tour" of this property', $listing['Listing']['Virtual_Tour_URL'], array('target' => '_blank')).'</p>';
	}
	if(!empty($secondary_images) && $secondary_images > 1){ 
		echo '<h2>Additional Images</h2>';
			$images = null;
			$img_count = 1;
			while($img_count < $secondary_images){
				$images[] = $html->image(array('controller' => 'listings', 'action' => 'image', $listing['Listing']['ML_Number_Display'].'/thumb/'.str_pad($img_count, 2, '0', STR_PAD_LEFT)), array('height' => 88, 'width' => 88));
				$img_count++;		
			}		
			$img_count = 1;
			foreach($images as $image){
				if($img_count < $secondary_images){
					$gallery_label = 'Click image for next in set.';
				} else {
					$gallery_label = 'Last image in set. Click image to close.';
				}
				echo $html->link($image, array('controller' => 'listings', 'action' => 'image', $listing['Listing']['ML_Number_Display'].'/full/'.str_pad($img_count, 2, '0', STR_PAD_LEFT).'.jpg'), array('class' => 'thickbox', 'rel' => 'listing-gallery', 'title' => $gallery_label), null, false);
				$img_count++;
				echo '&nbsp;';
			}
	}
	// end thumbnail output
	if(!empty($listing['Listing']['Street_Full_Address']) && (strtolower($listing['Listing']['RESI_Address_on_Internet_Desc']) == 'full')){ // show map
		$map = $listing['Listing'];
		$map_address = urlencode($map['Street_Number'].' '.$map['Street_Name'].' '.$map['Street_Suffix'].'., '.$map['City'].' '.$map['State'].' '.$map['Zip_Code']);
		echo '<h2>Map of Location</h2>';		
		echo '<iframe width="400" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>';
		echo '<p><i>Map information is approximate.</i></p>';
	//	echo '<iframe width="400" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="/mls/coordinates/map/'.$listing['Listing']['ML_Number_Display'].'"></iframe>';
		
	//	echo $html->link('Full Screen Map', array('controller' => 'coordinates', 'action' => 'map', $listing['Listing']['ML_Number_Display'].'/full?KeepThis=true&TB_iframe=true&height=600&width=800'), array('class' => 'thickbox'));
/*
<p><a href="/mls/coordinates/map/<?php echo $listing['Listing']['ML_Number_Display']; ?>/full" title="big_map" class="example2demo" name="windowX">View Larger Map</a></p>
<script type="text/javascript"> 
$('.example2demo').popupWindow({ 
centerBrowser:1,
height:600,
width:800,
}); 
</script>
*/

	//	echo 'GOOGLE MAP API DATA TO GO HERE';
		
		
	// debug($coordinates);



	}
	



	
	//echo $map;
	echo '</div> <!-- /ghdetailimgs -->';


	echo '<div id="ghdetailinfo">';
	
	echo $text->autoLink($listing['Listing']['Marketing_Remarks']);
	// PRINT THE PROPERTY INFORMATION TABLE
	echo '<table class="ghdetailstable" class="ghdetailstable">';
	foreach($grouping as $label => $group){
		if(!empty($group)){
			echo '<th colspan ="2">'.Inflector::humanize($label).'</th>'; // grouping header
			foreach($group as $sub_label => $item){
				$item = trim($item);
				if(!empty($item)){
					// special cases
					if($sub_label == 'City'){
						$item =  $html->link($item, array('controller' => 'listings', 'action' => 'city', Inflector::slug(strtolower($city['City']['name']))));
					} else if($sub_label == 'County'){
						$item =  $html->link($item, array('controller' => 'listings', 'action' => 'region', Inflector::slug(strtolower($city['County']['name']))));
					} else if ($sub_label == 'Zip'){
						$item =  $html->link($item, array('controller' => 'listings', 'action' => 'zip', $item));
					} else if ($sub_label == 'Agent Name' && !empty($listing['Listing']['Agent_Number'])){
						$item = $html->link($item, array('controller' => 'listings', 'action' => 'listing_agent', $listing['Listing']['Agent_Number']));
					}
					 else if ($sub_label == 'Listing Office' && !empty($listing['Listing']['Office_NRDS_ID'])){
						$item = $html->link($item, array('controller' => 'listings', 'action' => 'listing_office', $listing['Listing']['Office_NRDS_ID']));
					}
					 else if ($sub_label == 'Status' && !empty($listing['Listing']['Status'])){
						//$item = 'foo';
						if($listing['Listing']['Status'] == 'A'){
							$item = 'Active';
						} else if ($listing['Listing']['Status'] == 'D'){
							$item = 'Contingent';
						}
					}

					echo '<tr valign="top">';

					echo '<td nowrap>'.$sub_label.':</td>'; // item label
					echo '<td>'.$text->autoLink($item).'</td>';
					//echo '<td>'.$text->autoLink($item).'</td>'; // item value
					echo '</tr>';
				}
			} 
		}
	}
	echo '</table>';
	echo '</div> <!-- /ghdetailinfo -->';

		// update time info

			echo '<p><i>Property record updated ';
			echo $time->relativeTime($listing['Listing']['modified']);
			echo '. ';
			//echo 'Database last updated '.$time->relativetime($db_updated[0]['UpdateLog']['modified']).'.';
			echo '<br />BAREIS MLS Information herein believed reliable but not guaranteed.';
			echo '</i></p>'; 
	

	

		/*
		echo '<cake:nocache>';
			
			if(isset($this->params['user'])){
				echo '<div id="ghprevnext">';
				if(!empty($neighbors['prev'])){
					echo $html->link('Previous Property' , array('controller' => 'listings', 'action' => 'view', $neighbors['prev']['Listing']['ML_Number_Display']));
				}
				if(!empty($neighbors['prev'])){
					echo ' | '.$html->link('Next Property' , array('controller' => 'listings', 'action' => 'view', $neighbors['next']['Listing']['ML_Number_Display']));
				}
			}	else {
			
			}	

		echo '<cake:nocache>';
		*/

 

	//echo $this->element('map', array('listing' => $listing)); // to be implemented


/*
debug($listing);
*/



?>