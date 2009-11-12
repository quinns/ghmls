<? // debug($coordinate); ?>


<div class="coordinates view">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $coordinate['Coordinate']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MLS Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($coordinate['Coordinate']['ML_Number_Display'], array('admin' => 0, 'controller' => 'listings', 'action' => 'view', $coordinate['Coordinate']['ML_Number_Display'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Latitude'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $coordinate['Coordinate']['latitude']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Longitude'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $coordinate['Coordinate']['longitude']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $coordinate['Coordinate']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $coordinate['Coordinate']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Coordinate', true), array('action'=>'edit', $coordinate['Coordinate']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete Coordinate', true), array('action'=>'delete', $coordinate['Coordinate']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $coordinate['Coordinate']['id'])); ?> </li>
		<li><?php echo $html->link(__('List Coordinates', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Coordinate', true), array('action'=>'add')); ?> </li>
	</ul>
</div>

