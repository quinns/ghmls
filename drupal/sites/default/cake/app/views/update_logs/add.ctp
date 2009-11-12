<div class="UpdateLogs form">
<?php echo $form->create('UpdateLog');?>
	<fieldset>
 		<legend><?php __('Add UpdateLog');?></legend>
	<?php
		echo $form->input('data');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List UpdateLogs', true), array('action'=>'index'));?></li>
	</ul>
</div>
