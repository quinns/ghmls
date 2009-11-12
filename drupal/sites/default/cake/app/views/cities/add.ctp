<div class="cities form">
<?php echo $form->create('City');?>
	<fieldset>
 		<legend><?php __('Add City');?></legend>
	<?php
		echo $form->input('name');
		echo $form->input('county_id');
		echo $form->input('status_id');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Cities', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Counties', true), array('controller'=> 'counties', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New County', true), array('controller'=> 'counties', 'action'=>'add')); ?> </li>
	</ul>
</div>
