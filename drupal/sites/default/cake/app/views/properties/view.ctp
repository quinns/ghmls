
<cake:nocache>
<?php
/*
debug($property);
*/
if(isset($_SERVER['HTTP_REFERER'])){
	$referrer =  $_SERVER['HTTP_REFERER'];
	if(stristr($referrer, 'greathomes') !== false){
		echo '<p>'.$html->link('Return to Search Results', $referrer).'</p>';
	}
}

?>
</cake:nocache>


<?php
	echo '<!-- /#ghprevnext -->';
	echo '<h2>'.$this->pageTitle.'</h2>';
	echo '<div id="ghdetailimgs">';		
	echo $html->image(REMOTE_IMAGE_HOST.'properties/image/'.$property['Property']['ML_Number_Display'].'.jpg', $primary_image['attributes']);
	// start thumbnail output
	if(!empty($property['Property']['Virtual_Tour_URL'])){
		echo '<p>'.$html->link('See a "Virtual Tour" of this property', $property['Property']['Virtual_Tour_URL'], array('target' => '_blank')).'</p>';
	}
	if(!empty($secondary_images) && $secondary_images > 1){ 
		echo '<h2>Additional Images</h2>';
			$images = null;
			$img_count = 1;
			while($img_count < $secondary_images){
				$images[] = $html->image(REMOTE_IMAGE_HOST.'properties/image/'.$property['Property']['ML_Number_Display'].'/thumb/'.str_pad($img_count, 2, '0', STR_PAD_LEFT).'/image:thumb_'.$property['Property']['ML_Number_Display'].'_'.$img_count.'.jpg', array('height' => 88, 'width' => 88));
				$img_count++;		
			}		
			$img_count = 1;
			foreach($images as $image){
				if($img_count < $secondary_images){
					$gallery_label = 'Click image for next in set.';
				} else {
					$gallery_label = 'Last image in set. Click image to close.';
				}
				echo $html->link($image, REMOTE_IMAGE_HOST.'properties/image/'.$property['Property']['ML_Number_Display'].'/full/'.str_pad($img_count, 2, '0', STR_PAD_LEFT).'.jpg', array('class' => 'thickbox', 'rel' => 'listing-gallery', 'title' => $gallery_label, 'alt' => $gallery_label), null, false);
				$img_count++;
				echo '&nbsp;';
			}
	}
	// end thumbnail output
	if(!empty($property['Property']['Street_Full_Address']) && ((strtolower(@ $property['Property'][$address_display_field]) == 'full') || !isset($property['Property'][$address_display_field]))){ // show map
		$map = $property['Property'];
		$map_address = urlencode($map['Street_Number'].' '.$map['Street_Name'].' '.$map['Street_Suffix'].'., '.$map['City'].' '.$map['State'].' '.$map['Zip_Code']);
		echo '<h2>Map of Location</h2>';		
		echo '<iframe width="380" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>';
		echo '<p><i>Map information is approximate.</i></p>';
	}
	echo '</div> <!-- /ghdetailimgs -->';
	echo '<div id="ghdetailinfo">';
	echo $text->autoLink($property['Property']['Marketing_Remarks']);
	// PRINT THE PROPERTY INFORMATION TABLE
	echo $this->element('addthis');
	echo '<table class="ghdetailstable" class="ghdetailstable">';
	foreach($grouping as $label => $group){
		if(!empty($group)){
			echo '<th colspan ="2">'.Inflector::humanize($label).'</th>'; // grouping header
			foreach($group as $sub_label => $item){
				$item = trim($item);
				if(!empty($item)){
					// special cases
					$form_url = 'http://'.$_SERVER['SERVER_NAME'].'/contact-agent';
					if($sub_label == 'City'){
						$item =  $html->link($item, array('controller' => 'properties', 'action' => 'city', Inflector::slug(strtolower($city['City']['name']))));
					} else if($sub_label == 'County'){
						$item =  $html->link($item, array('controller' => 'properties', 'action' => 'region', Inflector::slug(strtolower($city['County']['name']))));
					} else if ($sub_label == 'Zip'){
						$item =  $html->link($item, array('controller' => 'properties', 'action' => 'zip', $item));
					} else if ($sub_label == 'Listing Agent' && !empty($property['Property']['Agent_Number'])){
						if(!empty($property['Property']['Agent_License'])){
							$item = $html->link($item, array('controller' => 'properties', 'action' => 'listing_agent', $property['Property']['Agent_Number'])).' (Lic: '.$property['Property']['Agent_License'].')';
						}			
					}
					 else if ($sub_label == 'Listing Office' && !empty($property['Property']['Office_NRDS_ID'])){
					 
					 	if(!empty($member_office['MemberOffice']['Office_Corporate_License'])){
						$item = $html->link($item, array('controller' => 'properties', 'action' => 'listing_office', $property['Property']['Office_NRDS_ID']));
						/* ------- */
						$item .= ' (Lic: '.$member_office['MemberOffice']['Office_Corporate_License'].')';
						}
			
					}
					 else if ($sub_label == 'Listing Office' && !empty($property['Property']['Office_NRDS_ID'])){
						$item = $html->link($item, array('controller' => 'properties', 'action' => 'listing_office', $property['Property']['Office_NRDS_ID']));
					}	
					 else if ($sub_label == 'Listing Agent Email' && !empty($property['Property']['Agent_Email_Address'])){
						$item = $form->create('Property', array('url' => $form_url));
						$item = '<form method="GET" action="/contact-agent">';
						$item.= '<input type="hidden" name="recipient_name" value="'.$property['Property']['Agent_Name'].'">';					
						$item.= '<input type="hidden" name="mls_id" value="'.$property['Property']['ML_Number_Display'].'">';					
						$recipient_address = $property['Property']['Agent_Email_Address'];
						$item.= '<input type="hidden" name="recipient_email" value="'.$recipient_address.'">';
						$item.= $form->end('Click to Contact Agent');										
					}
					 else if ($sub_label == 'Co-Listing Agent Email' && !empty($property['Property']['Co_Agent_Email_Address'])){
						$item = $form->create('Property', array('url' => $form_url));
						$item = '<form method="GET" action="/contact-agent">';
						$item.= '<input type="hidden" name="recipient_name" value="'.$grouping['Contact_Information']['Co-Listing Agent'].'">';				
						$recipient_address = $property['Property']['Co_Agent_Email_Address'];
						$item.= '<input type="hidden" name="recipient_email" value="'.$recipient_address.'">';
						$item.= $form->end('Click to Contact Co-Agent');										
					}
					 else if ($sub_label == 'Status' && !empty($property['Property']['Status'])){
						//$item = 'foo';
						if($property['Property']['Status'] == 'A'){
							$item = 'Active';
						} else if ($property['Property']['Status'] == 'D'){
							$item = 'Contingent';
						}
					}

					echo '<tr valign="top">';

					echo '<td nowrap>'.$sub_label.':</td>'; // item label
					if($sub_label == 'Listing Agent Email' || $sub_label == 'Co-Listing Agent Email'){
						echo '<td>'.$item.'</td>';
					} else {
						echo '<td>'.$text->autoLink($item).'</td>';
					}
					echo '</tr>';
				}
			} 
		}
	}
	echo '</table>';
	
	
		// start open house info
	if (!empty($property['Property']['Open_House_Start_Date'])){
		$open_house['Open_House_Comments'] = $property['Property']['Open_House_Comments'];
		$open_house['Open_House_End_Time'] = $property['Property']['Open_House_End_Time'];
		$open_house['Open_House_Hosted'] = $property['Property']['Open_House_Hosted'];
		$open_house['Open_House_Start_Date'] = $property['Property']['Open_House_Start_Date'];
		$open_house['Open_House_Start_Time'] = $property['Property']['Open_House_Start_Time'];
		$open_house['Open_House_Time_Comments'] = $property['Property']['Open_House_Time_Comments'];
		$open_house['Open_House_Start_Timestamp'] = $property['Property']['Open_House_Start_Timestamp'];
		$open_house['Open_House_End_Timestamp'] = $property['Property']['Open_House_End_Timestamp'];
/*
		debug($open_house);
*/
		$now = time();
/*
		$start_time = $open_house['Open_House_Start_Date'].' '.$open_house['Open_House_Start_Time'];

		$start_timestamp = strtotime($start_time);
*/
		$start_timestamp = $open_house['Open_House_Start_Timestamp'];
		$end_timestamp = $open_house['Open_House_End_Timestamp'];		
		$end_time =  $open_house['Open_House_Start_Date'].' '.$open_house['Open_House_End_Time'];
		$end_timestamp = strtotime($end_time);
		if($end_timestamp > $now){ // print open house data if the end-time is in the future
			echo '<a name="open_house"></a>';
			echo '<fieldset><legend><b>Open House Information</b></legend>';
				if($time->isToday($start_timestamp)){
					echo '<p><b>Today!</b></p>';
				}
				echo '<p><b>'.date("l, F d, Y g:i a", strtotime($start_timestamp)).' &mdash; '.date("g:i a", strtotime($end_timestamp)).'</b></p>';
/*
				echo('<p>Open House Starts: '.$time->relativeTime($start_timestamp)).'</p>';
				$open_house['Open_House_Time_Comments'] = '<p>Time comments would go here.</p>';
				if(!empty($open_house['Open_House_Time_Comments'])){
					echo '<i>'.$open_house['Open_House_Time_Comments'].'</i>';
				}
				if(!empty($open_house['Open_House_Hosted']) && strtolower($open_house['Open_House_Hosted'][0]) == 'y'){
					echo '<p>Hosted: Yes</p>';
				}
*/

				if($open_house['Open_House_Comments'] != $property['Property']['Marketing_Remarks']){
					echo '<p>'.$open_house['Open_House_Comments'].'</p>';
				}
			echo '</fieldset>';
		}
	} else {
		echo '<br /><br />';
	}
	// end open house info
	
	
	echo '</div> <!-- /ghdetailinfo -->';

		// update time info
			echo '<div class="property_footer">';
			echo '<p><i>Property record updated ';
			echo $time->relativeTime($property['Property']['modified']);
			echo '. ';
			//echo 'Database last updated '.$time->relativetime($db_updated[0]['UpdateLog']['modified']).'.';
			echo '<br />BAREIS MLS Information herein believed reliable but not guaranteed.';
			echo '</i></p>'; 
			echo '</div>';
	

	

		/*
		echo '<cake:nocache>';
			
			if(isset($this->params['user'])){
				echo '<div id="ghprevnext">';
				if(!empty($neighbors['prev'])){
					echo $html->link('Previous Property' , array('controller' => 'properties', 'action' => 'view', $neighbors['prev']['Property']['ML_Number_Display']));
				}
				if(!empty($neighbors['prev'])){
					echo ' | '.$html->link('Next Property' , array('controller' => 'properties', 'action' => 'view', $neighbors['next']['Property']['ML_Number_Display']));
				}
			}	else {
			
			}	

		echo '<cake:nocache>';
		*/

 

	//echo $this->element('map', array('property' => $property)); // to be implemented








/*
debug($property);
*/




?>