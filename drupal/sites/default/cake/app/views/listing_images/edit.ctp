<div class="listingImages form">
<?php echo $form->create('ListingImage');?>
	<fieldset>
 		<legend><?php __('Edit ListingImage');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('mls_number');
		echo $form->input('c2');
		echo $form->input('c3');
		echo $form->input('remote_url');
		echo $form->input('local_path');
		echo $form->input('c6');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
	<? /*	<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('ListingImage.mls_number')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('ListingImage.mls_number'))); ?></li> */ ?>
		<li><?php echo $html->link(__('List ListingImages', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Listings', true), array('controller'=> 'listings', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Listing', true), array('controller'=> 'listings', 'action'=>'add')); ?> </li>
	</ul>
</div>
