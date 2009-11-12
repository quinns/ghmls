<div class="listingImages view">
<h2><?php echo $this->pageTitle; ?></h2>
<?php
	echo $this->element('listing_image', array('id' => $listingImage['ListingImage']['id']));
?>
</div>
<div class="related">
	<h3><?php  __('Related Listing');?></h3>
<?php if (!empty($listingImage['Listing'])):?>
	<dl>	<?php $i = 0; $class = ' class="altrow"';?>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('County');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['county'];?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('City');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php  echo $listingImage['Listing']['city']; ?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Zip Code');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['zip_code'];?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Type');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['listing_type'];?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Price');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $number->currency($listingImage['Listing']['listing_price']);?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bedrooms');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['bedrooms'];?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bathrooms');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['bathrooms'];?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Mls Number');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $html->link($listingImage['Listing']['mls_number'], array('controller' => 'listings', 'action' => 'view', $listingImage['Listing']['mls_number']));?>
&nbsp;</dd>
	<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status');?></dt>
	<dd<?php if ($i++ % 2 == 0) echo $class;?>>
<?php echo $listingImage['Listing']['status'];?>
&nbsp;</dd>
	</dl>
<?php endif; ?>
</div>
	
