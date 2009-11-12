<div class="counties index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('status_id');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($counties as $county):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $county['County']['id']; ?>
		</td>
		<td>
			<?php echo $html->link($county['County']['name'], array('controller' => 'counties', 'action' => 'view', $county['County']['id'])); 
				echo ' ('.$number->format($county['City']['count']).')';
			?> 
		</td>
		<td>
			<?php echo $this->element('status', array('status_id' =>$county['County']['status_id'])); ?>
		</td>
		<td>
			<?php echo $county['County']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $county['County']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $county['County']['id'])); ?>
			<?php // echo $html->link(__('Delete', true), array('action'=>'delete', $county['County']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $county['County']['id'])); ?>
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
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New County', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>


