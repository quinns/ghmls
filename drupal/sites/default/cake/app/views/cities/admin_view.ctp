<div class="cities view">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $city['City']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('County'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($city['County']['name'], array('controller'=> 'counties', 'action'=>'view', $city['County']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->element('status' , array('status_id' => $city['City']['status_id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $city['City']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $city['City']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Browse Properties', array('controller' => 'listings', 'action' => 'city', 'admin' => 0, $city['City']['id'])); ?></li>
		<li><?php echo $html->link(__('Edit City', true), array('action'=>'edit', $city['City']['id'])); ?> </li>
		<? /* <li><?php echo $html->link(__('Delete City', true), array('action'=>'delete', $city['City']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $city['City']['id'])); ?> </li> */ ?>
		<li><?php echo $html->link(__('List Cities', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Counties', true), array('controller'=> 'counties', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New County', true), array('controller'=> 'counties', 'action'=>'add')); ?> </li>
	</ul>
</div>
