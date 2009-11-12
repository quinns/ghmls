<div class="counties form">

<?php echo $form->create('County');?>
	<fieldset>
 		<legend><?php __('Edit County');?></legend>
	<? 
		echo $form->input('id');
		echo $form->input('name');
		if(!empty($cities_in_county)){
		//	echo 'Cities in this county: <br /> ';
			foreach($cities_in_county as $key => $value){
				echo $html->link($value, array('controller' => 'cities', 'action' => 'view', $key)).' ';
			}
		}
		/*echo '<br />';
		echo 'Cities in other counties (not available to add to this county): ';
		foreach($cities_outside_county as $key => $value){
			echo $html->link($value, array('controller' => 'cities', 'action' => 'view', $key)).' ';
		}
		*/


		/*
		debug($cities_in_county);
		debug($cities_outside_county);
		*/

		echo $form->input('City', array('type' => 'select', 'multiple' => 'checkbox', 'label' => 'Cities in '.$html->link($form->value('County.name'), array('controller' => 'counties', 'action' => 'view', $form->value('County.id'))).'
	 County'));
		echo $form->input('status_id', array('label' => 'Active'));
		
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
	<? /*	<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('County.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('County.id'))); ?></li> */ ?>
		<li><?php echo $html->link(__('List Counties', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>