<div class="UpdateLogs view">
<h2><?php  __('UpdateLog');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $UpdateLog['UpdateLog']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Data'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $UpdateLog['UpdateLog']['data']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $UpdateLog['UpdateLog']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $UpdateLog['UpdateLog']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit UpdateLog', true), array('action'=>'edit', $UpdateLog['UpdateLog']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete UpdateLog', true), array('action'=>'delete', $UpdateLog['UpdateLog']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $UpdateLog['UpdateLog']['id'])); ?> </li>
		<li><?php echo $html->link(__('List UpdateLogs', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New UpdateLog', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
