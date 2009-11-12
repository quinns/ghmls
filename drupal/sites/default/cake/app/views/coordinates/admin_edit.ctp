<div class="coordinates form">
<?php echo $form->create('Coordinate');?>
	<fieldset>
 		<legend><?php __('Edit Coordinate');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('listing_id');
		echo $form->input('latitude');
		echo $form->input('longitude');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Coordinate.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Coordinate.id'))); ?></li>
		<li><?php echo $html->link(__('List Coordinates', true), array('action'=>'index'));?></li>
	</ul>
</div>
