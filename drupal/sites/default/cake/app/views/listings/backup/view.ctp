<div class="listings view">
<h2><?php echo $this->pageTitle; ?></h2>
<div id="ghdetailimgs">
<img src="http://greathomes.org/mls/image/158899" />
	<div class="related">
		<h3><?php __('Additional Images');?></h3>
		<?php // render any images associated with this listing
		 if (!empty($listing['ListingImage'])):
			foreach($listing['ListingImage'] as $listingImage){
				$image['ListingImage'] = $listingImage;
				echo $this->element('listing_thumbnail', array('listingImage' => $image));
				echo ' ';
			}
		?>	
	<?php endif; ?>
	</div> <!-- /.related -->
</div> <!-- /#detailimgs -->
<div id="ghdetailinfo">
<?php
	//echo $this->element('map', array('listing' => $listing)); // to be implemented
	if(empty($listing['Listing']['street_name'])){
	//	echo '<h3>Note: Map is approximate as this property does not provide a full street address.</h3>'; // to be implemented
	}	 
	// this display code is only temporary and will need to be reformatted
	echo '<table class="ghdetailstable">';
	ksort($listing['Listing']);
	$listing['Listing']['listing_price'] = $number->currency($listing['Listing']['listing_price']);
	$listing['Listing']['date_modified'] = $time->nice($listing['Listing']['date_modified']);
	foreach($listing['Listing'] as $key => $value){
	//	if(!empty($value)){ // currently display all fields even if they are empty
		echo '<tr><td>'.Inflector::humanize($key).':</td>';
		if($value == $listing['City']['name']){
			echo '<td>'.$html->link($value, array('controller' => 'listings', 'action' => 'city', $listing['City']['id'])).'</td>';
		} else if ($value == $listing['County']['name']) {
			echo '<td>'.$html->link($value, array('controller' => 'listings', 'action' => 'region', $listing['County']['id'])).'</td>';
		} else {
			echo '<td>'.$value.'</td></tr>';
		}
	//	} // end empty field display
	}
	echo '</table>';

 /*
 	// following is scaffold output
 	
	<dl><?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MLS Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['mls_number']; ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['street_number'].' '.$listing['Listing']['street_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('City'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['city']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Zip Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['zip_code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('County'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['county']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Senior'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['senior']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['listing_type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Price'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $number->currency($listing['Listing']['listing_price']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bedrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['bedrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bathrooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['bathrooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Address On Internet'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['address_on_internet']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Publish To Internet'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['publish_to_internet']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $listing['Listing']['status']; ?>
			&nbsp;
		</dd>
	</dl>
	*/ ?>
</div> <!-- /#ghdetailinfo -->
</div> <!-- /.listings view -->