<div class="UpdateLogs view">
	


	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Log Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $time->nice($UpdateLog['UpdateLog']['modified']).' ('.$time->relativeTime($UpdateLog['UpdateLog']['modified']).')'; ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Summary'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $UpdateLog['UpdateLog']['records_updated']; ?> record(s) added or updated
			&nbsp;
		</dd>

		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Processing Time'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo  $number->precision($UpdateLog['UpdateLog']['processing_time'], 4); ?> second(s)
			&nbsp;
		</dd>
				
				
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Memory Usage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo  $number->toReadableSize($UpdateLog['UpdateLog']['memory_usage']); ?>
			&nbsp;
		</dd>
		
	</dl>
	<br />
	<h1><?php echo $html->link('Complete Log Details', array('controller' => 'update_logs', 'action' => 'plaintext', 'admin' => 1,  $UpdateLog['UpdateLog']['id']), array('target' => '_blank')); ?></h1>
<iframe src ="/mls/admin/update_logs/plaintext/<?php echo  $UpdateLog['UpdateLog']['id']; ?>" width="100%" height="500">
 <p>Your browser does not support iframes.</p>
</iframe>

</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete Update Log Entry', true), array('action'=>'delete', $UpdateLog['UpdateLog']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $UpdateLog['UpdateLog']['id'])); ?> </li>
		<li><?php echo $html->link(__('List Update Logs', true), array('action'=>'index')); ?> </li>
	</ul>
</div>

<?php // debug($UpdateLog); ?>
<? echo $this->element('admin_user'); ?>