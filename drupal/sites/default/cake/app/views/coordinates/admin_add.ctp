<div class="coordinates form">
<?php echo $form->create('Coordinate');?>
	<fieldset>
 		<legend><?php __('Add Coordinate');?></legend>
	<?php
		echo $form->input('listing_id');
		echo $form->input('latitude');
		echo $form->input('longitude');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Coordinates', true), array('action'=>'index'));?></li>
	</ul>
</div>
