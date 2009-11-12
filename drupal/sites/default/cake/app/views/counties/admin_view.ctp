<div class="counties view">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $county['County']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->element('status', array('status_id' => $county['County']['status_id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $county['County']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $county['County']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
<li><?	echo ' '.$html->link('Browse Properties', array('controller' => 'listings', 'action' => 'region', 'admin' => 0, $county['County']['id'])); ?></li>


		<li><?php echo $html->link(__('Edit County', true), array('action'=>'edit', $county['County']['id'])); ?> </li>
	<? /*	<li><?php echo $html->link(__('Delete County', true), array('action'=>'delete', $county['County']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $county['County']['id'])); ?> </li> */ ?>
		<li><?php echo $html->link(__('List Counties', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New County', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Cities');?></h3>
	<?php if (!empty($county['City'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Status'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($county['City'] as $city):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $city['id'];?></td>
			<td><?php echo $html->link($city['name'], array('controller' => 'cities', 'action' => 'view', $city['id']));?></td>
			<td><?php echo $this->element('status', array('status_id' => $city['status_id']));?></td>
			<td><?php echo $city['modified'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'cities', 'action'=>'view', $city['id'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'cities', 'action'=>'edit', $city['id'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'cities', 'action'=>'delete', $city['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $city['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>

<? // debug($county); ?>