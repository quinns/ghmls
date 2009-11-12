<? $paginator->options(array('url'=>array_merge(array('first_name'=>$first_name, 'last_name' => $last_name),$this->passedArgs))); ?>
<h2>Search for Agents</h2>
<?
/* 	echo '<h2>Search for Agents</h2>'; */
	echo $form->create('MemberAgent', array('action' => 'index', 'type' => 'get'));
	echo $form->input('first_name', array('div' => false, 'value' => $first_name));
	echo $form->input('last_name', array('div' => false, 'value' => $last_name));
	echo $form->input('office', array('div' => false, 'value' => $office));
	echo $form->input('city', array('div' => false, 'value' => $city));
/* 	echo $form->end('Search', array('div' => false)); */
	echo $form->end(array('label' => 'Search', 'div' => false));
	if(!empty($first_name) || !empty($last_name)){
		echo $html->link('New Search', array('action' => 'index'));
	}
	echo ' <br />';
	echo $html->link('Search for Office', array('controller' => 'member_offices', 'action' => 'index'));
	echo '<br /><br />';
?>

<?php 
/* debug($conditions); */
if (!empty($conditions) || (!empty($this->params['named']['page']) || !empty($this->params['named']['sort']))) { ?>


<div class="mlsAgents index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0" class="ghresultstable" width="100%">
<tr>
	<th><?php echo $paginator->sort('First Name', 'Agent_First_Name');?>&nbsp;</th>
	<th><?php echo $paginator->sort('Last Name', 'Agent_Last_Name');?>&nbsp;</th>
	<th>Phone Number&nbsp;</th>
	<th><?php echo $paginator->sort('Office', 'MemberOffice.Office_Long_Name');?>&nbsp;</th>
	<th><?php echo $paginator->sort('City', 'MemberOffice.Office_City');?>&nbsp;</th>
	<th>Properties&nbsp;</th> 
	<th>Action</th>
</tr>
<?php
$i = 0;
foreach ($memberAgents as $memberAgent):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>


		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_First_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Last_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Phone_1']; ?>
		</td>
		<td>
			<?php 
			
			if(!empty($memberAgent['MemberOffice']['Office_Long_Name'])){
			
			echo $html->link($memberAgent['MemberOffice']['Office_Long_Name'], array('controller' => 'properties', 'action' => 'listing_office', $memberAgent['MemberAgent']['Office_MLS_ID']));
			
			} 
			
			?>
		</td>
		<td>
			<?php echo $memberAgent['MemberOffice']['Office_City']; ?>
		</td>
		<td>
			<?php echo count($memberAgent['Property']); ?>
		</td>
		<td>
		<?php echo $html->link('See Details', array('action'=>'view', $memberAgent['MemberAgent']['Agent_MLS_ID'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div> 
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>



<? } ?>

<? 
/* debug($memberAgents[0]); */
 ?>
 <br />