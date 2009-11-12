<div class="counties form">
<?php echo $form->create('County');?>
	<fieldset>
 		<legend><?php __('Edit County');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('name');
		echo $form->input('status_id');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php // echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('County.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('County.id'))); ?></li>
		<li><?php echo $html->link(__('List Counties', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
