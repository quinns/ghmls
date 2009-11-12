
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
	
	echo $html->image(array('controller' => 'listings', 'action' => 'image', $BusinessOpp['BusinessOpp']['ML_Number_Display'].'.jpg'), $primary_image['attributes']);
	// start thumbnail output
	if(!empty($BusinessOpp['BusinessOpp']['Virtual_Tour_URL'])){
		echo '<p>'.$html->link('See a "Virtual Tour" of this property', $BusinessOpp['BusinessOpp']['Virtual_Tour_URL'], array('target' => '_blank')).'</p>';
	}
	if(!empty($secondary_images) && $secondary_images > 1){ 
		echo '<h2>Additional Images</h2>';
			$images = null;
			$img_count = 1;
			while($img_count < $secondary_images){
				$images[] = $html->image(array('controller' => 'listings', 'action' => 'image', $BusinessOpp['BusinessOpp']['ML_Number_Display'].'/thumb/'.str_pad($img_count, 2, '0', STR_PAD_LEFT)), array('height' => 88, 'width' => 88));
				$img_count++;		
			}		
			$img_count = 1;
			foreach($images as $image){
				if($img_count < $secondary_images){
					$gallery_label = 'Click image for next in set.';
				} else {
					$gallery_label = 'Last image in set. Click image to close.';
				}
				echo $html->link($image, array('controller' => 'listings', 'action' => 'image', $BusinessOpp['BusinessOpp']['ML_Number_Display'].'/full/'.str_pad($img_count, 2, '0', STR_PAD_LEFT).'.jpg'), array('class' => 'thickbox', 'rel' => 'listing-gallery', 'title' => $gallery_label), null, false);
				$img_count++;
				echo '&nbsp;';
			}
	}
	// end thumbnail output
	if(!empty($BusinessOpp['BusinessOpp']['Street_Full_Address'])){ // show map
		$map = $BusinessOpp['BusinessOpp'];
		$map_address = urlencode($map['Street_Number'].' '.$map['Street_Name'].' '.$map['Street_Suffix'].'., '.$map['City'].' '.$map['State'].' '.$map['Zip_Code']);
		echo '<h2>Map of Location</h2>';		
		echo '<iframe width="400" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$map_address.'&amp;z=14&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>';
		echo '<p><i>Map information is approximate.</i></p>';
	//	echo '<iframe width="400" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="/mls/coordinates/map/'.$BusinessOpp['BusinessOpp']['ML_Number_Display'].'"></iframe>';
		
	//	echo $html->link('Full Screen Map', array('controller' => 'coordinates', 'action' => 'map', $BusinessOpp['BusinessOpp']['ML_Number_Display'].'/full?KeepThis=true&TB_iframe=true&height=600&width=800'), array('class' => 'thickbox'));
/*
<p><a href="/mls/coordinates/map/<?php echo $BusinessOpp['BusinessOpp']['ML_Number_Display']; ?>/full" title="big_map" class="example2demo" name="windowX">View Larger Map</a></p>
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
	
	echo $text->autoLink($BusinessOpp['BusinessOpp']['Marketing_Remarks']);
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
						$item =  $html->link($item, array('controller' => 'business_opps', 'action' => 'city', Inflector::slug(strtolower($city['City']['name']))));
					} else if($sub_label == 'County'){
						$item =  $html->link($item, array('controller' => 'business_opps', 'action' => 'region', Inflector::slug(strtolower($city['County']['name']))));
					} else if ($sub_label == 'Zip'){
						$item =  $html->link($item, array('controller' => 'business_opps', 'action' => 'zip', $item));
					} else if ($sub_label == 'Agent Name' && !empty($BusinessOpp['BusinessOpp']['Agent_Number'])){
						$item = $html->link($item, array('controller' => 'business_opps', 'action' => 'listing_agent', $BusinessOpp['BusinessOpp']['Agent_Number']));
					}
					 else if ($sub_label == 'Listing Office' && !empty($BusinessOpp['BusinessOpp']['Office_NRDS_ID'])){
						$item = $html->link($item, array('controller' => 'business_opps', 'action' => 'listing_office', $BusinessOpp['BusinessOpp']['Office_NRDS_ID']));
					}
					 else if ($sub_label == 'Status' && !empty($BusinessOpp['BusinessOpp']['Status'])){
						//$item = 'foo';
						if($BusinessOpp['BusinessOpp']['Status'] == 'A'){
							$item = 'Active';
						} else if ($BusinessOpp['BusinessOpp']['Status'] == 'D'){
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
			echo $time->relativeTime($BusinessOpp['BusinessOpp']['modified']);
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

<?php
/* Original baked version */
/*
<div class="BusinessOpps view">
<h2><?php  __('BusinessOpp');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ML Number Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['ML_Number_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Acres'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Acres']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('City'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['City']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('State'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['State']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Street Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Street Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Street Suffix'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Suffix']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Zip Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Zip_Code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Email Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Email_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent First Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_First_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Last Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Last_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent NRDS ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_NRDS_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Area Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bathrooms Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Bathrooms_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bedrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Bedrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('County'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['County']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cross Street'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Cross_Street']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Full Bathrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Full_Bathrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Half Bathrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Half_Bathrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IDX'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['IDX']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Agent Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Agent_Email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lot Size Sq Ft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Sq_Ft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lot Size Acres'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Acres']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lot Size Source'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Source']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Map Coordinates'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Coordinates']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Map Page'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Marketing Remarks'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Marketing_Remarks']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Broker ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Broker_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Full Name And Phones'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Full_Name_and_Phones']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Long Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Long_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office NRDS ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_NRDS_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Short Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Short_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Phone 1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Phone_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Property Subtype 1 Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Subtype_1_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Property Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Publish To Internet'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Publish_to_Internet']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Search Price'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Search Price Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Square Footage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Square Footage And Source'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_and_Source']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Square Footage Source'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_Source']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Status']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Street Direction'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Direction']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Street Full Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Full_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Subdivision Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Subdivision_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Total Bathrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Total_Bathrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Virtual Tour URL'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Virtual_Tour_URL']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Year Built'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Year_Built']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Nickname'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Nickname']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Office Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Office_Number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Area'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Area']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lot Size'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Map Book'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Map Book Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Map Page And Coordinates'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page_and_Coordinates']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MLS'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['MLS']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Pictures Count'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Pictures_Count']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Primary Picture Filename'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Filename']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Primary Picture Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Office Fax Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Office_Fax_Phone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Transaction Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Transaction_Type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Area Short Display'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Short_Display']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Phones'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phones']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Phone 1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phone_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Reciprocal Member Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Reciprocal Member Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Phone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Reciprocal Office Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Office_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Agent Email Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Email_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Agent Fax Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Fax_Phone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Agent Full Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Full_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Agent Phone Type 1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Phone_Type_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Agent Web Page Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Web_Page_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Office Fax Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Fax_Phone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Co Office Full Name And Phones'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Full_Name_and_Phones']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Web Page Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Web_Page_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $BusinessOpp['BusinessOpp']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit BusinessOpp', true), array('action'=>'edit', $BusinessOpp['BusinessOpp']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete BusinessOpp', true), array('action'=>'delete', $BusinessOpp['BusinessOpp']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $BusinessOpp['BusinessOpp']['id'])); ?> </li>
		<li><?php echo $html->link(__('List BusinessOpps', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New BusinessOpp', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
*/

?>
