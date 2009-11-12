<div class="UpdateLogs form">
<?php echo $form->create('UpdateLog');?>
	<fieldset>
 		<legend><?php __('Edit UpdateLog');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('data');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('UpdateLog.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('UpdateLog.id'))); ?></li>
		<li><?php echo $html->link(__('List UpdateLogs', true), array('action'=>'index'));?></li>
	</ul>
</div>
