<div class="cities index">
<h2><?php __('Cities');?></h2>
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
	<th><?php echo $paginator->sort('county_id');?></th>
	<th><?php echo $paginator->sort('status_id');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($cities as $city):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $city['City']['id']; ?>
		</td>
		<td>
			<?php echo $city['City']['name']; ?>
		</td>
		<td>
			<?php echo $html->link($city['County']['name'], array('controller'=> 'counties', 'action'=>'view', $city['County']['id'])); ?>
		</td>
		<td>
			<?php echo $city['City']['status_id']; ?>
		</td>
		<td>
			<?php echo $city['City']['created']; ?>
		</td>
		<td>
			<?php echo $city['City']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $city['City']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $city['City']['id'])); ?>
			<?php // echo $html->link(__('Delete', true), array('action'=>'delete', $city['City']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $city['City']['id'])); ?>
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
		<li><?php echo $html->link(__('New City', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Counties', true), array('controller'=> 'counties', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New County', true), array('controller'=> 'counties', 'action'=>'add')); ?> </li>
	</ul>
</div>
