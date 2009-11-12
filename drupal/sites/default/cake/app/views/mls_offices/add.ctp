<div class="mlsOffices form">
<?php echo $form->create('MlsOffice');?>
	<fieldset>
 		<legend><?php __('Add MlsOffice');?></legend>
	<?php
		echo $form->input('Office_MLS_ID');
		echo $form->input('Office_Long_Name');
		echo $form->input('Office_Care_of');
		echo $form->input('Office_Address');
		echo $form->input('Office_City');
		echo $form->input('Office_Zip_Code');
		echo $form->input('Office_Zip_Plus_4');
		echo $form->input('Office_State');
		echo $form->input('Office_Status');
		echo $form->input('Office_Class_Type');
		echo $form->input('Office_Phone_1');
		echo $form->input('Office_BR');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List MlsOffices', true), array('action'=>'index'));?></li>
	</ul>
</div>
