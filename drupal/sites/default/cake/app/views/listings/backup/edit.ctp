<div class="listings form">
<?php echo $form->create('Listing');?>
	<fieldset>
 		<legend><?php __('Edit Listing');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('county');
		echo $form->input('senior');
		echo $form->input('city');
		echo $form->input('zip_code');
		echo $form->input('listing_type');
		echo $form->input('listing_price');
		echo $form->input('bedrooms');
		echo $form->input('bathrooms');
		echo $form->input('mls_number');
		echo $form->input('address_on_internet');
		echo $form->input('idx');
		echo $form->input('publish_to_internet');
		echo $form->input('status');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
	<? /*	<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Listing.mls_number')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Listing.mls_number'))); ?></li> */ ?>
		<li><?php echo $html->link(__('List Listings', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Listing Images', true), array('controller'=> 'listing_images', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Listing Image', true), array('controller'=> 'listing_images', 'action'=>'add')); ?> </li>
	</ul>
</div>
