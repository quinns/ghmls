<? $paginator->options(array('url'=>array_merge(array('office'=>$office, 'city' => $city),$this->passedArgs))); 
?>

<h2><?php __('Search for Offices');?></h2>
<?
/* 	echo '<h3>Search for Offices</h3>'; */
	echo $form->create('MemberOffice', array('action' => 'index', 'type' => 'get'));
	echo $form->input('office', array('div' => false, 'value' => $office));
	echo $form->input('city', array('div' => false, 'value' => $city));
/* 	echo $form->end('Search', array('div' => false)); */
	echo $form->end(array('label' => 'Search', 'div' => false));
	if(!empty($city) || !empty($office)){
		echo $html->link('New Search', array('action' => 'index'));
	}
?><br />



<?php
	echo $html->link('Search for Agents', array('controller' => 'member_agents', 'action' => 'index'));
	echo '<br />';
/* debug($this->params); */

 if (!empty($this->params['named']['page']) || !empty($this->params['named']['sort']) || isset($this->params['url']['office']) || isset($this->params['url']['city'])) { ?>
<div class="mlsOffices index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0" width="100%"  class="ghresultstable">  
<tr>
	<th><?php echo $paginator->sort('Office Name', 'Office_Long_Name');?>&nbsp;</th>
	<th><?php echo $paginator->sort('Office_City');?>&nbsp;</th>
	<th><?php echo $paginator->sort('Office_State');?>&nbsp;</th>
	<th>Properties&nbsp;</th>
	<th><?php echo $paginator->sort('Office Phone', 'Office_Phone_1');?>&nbsp;</th>
</tr>
<?php
$i = 0;
foreach ($memberOffices as $memberOffice):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
			<?php echo $html->link($memberOffice['MemberOffice']['Office_Long_Name'], array('controller' => 'member_offices', 'action' => 'view', $memberOffice['MemberOffice']['Office_MLS_ID'])); ?>
		</td>
		<td>
			<?php echo $memberOffice['MemberOffice']['Office_City']; ?>
		</td>
		<td>
			<?php echo $memberOffice['MemberOffice']['Office_State']; ?>
		</td>
				<td>
			<?php echo count($memberOffice['Property']); ?>
		</td>
		<td>
			<?php echo $memberOffice['MemberOffice']['Office_Phone_1']; ?>
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
<br />
<? 
debug($memberOffices[0]);
 ?>