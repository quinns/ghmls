<?php
	$this->pageTitle = 'Agent Detail: '.$memberAgent['MemberAgent']['Agent_First_Name'].' '.$memberAgent['MemberAgent']['Agent_Last_Name'];
?>


<div class="mlsAgents view">
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent MLS ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_MLS_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent First Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_First_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Last Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_Last_Name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Middle Initial'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_Middle_Initial']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Phone 1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_Phone_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office MLS ID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Office_MLS_ID']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Class Bill Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_Class_Bill_Type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Email Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $text->autoLink($memberAgent['MemberAgent']['Agent_Email_Address']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Web Page Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $text->autoLink($memberAgent['MemberAgent']['Agent_Web_Page_Address']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent License'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($memberAgent['MemberAgent']['Agent_License'], array('controller' => 'properties', 'action' => 'listing_agent', 'admin' => 0, $memberAgent['MemberAgent']['Agent_License'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<?php if (!empty($memberAgent['Property'])) { ?>
<h2>Properties Represented by this Agent</h2>
<table>
<tr>
<th>Image</th>
<th>City</th>
<th>County</th>
<th>Price</th>
<th>Updated</th>
</tr>
<?php foreach($memberAgent['Property'] as $property){ ?>

<tr>
<td><?php echo $html->link($html->image(array('controller' => 'properties', 'admin' => 0, 'action' => 'image', $property['ML_Number_Display'].'.jpg'), array('width' => 88)), array('admin' => 0, 'controller' => 'properties', 'action' => 'view', $property['ML_Number_Display']), null, null, false); ?></td>
<td><?php echo $html->link($property['City'], array('admin' => 0, 'controller' => 'properties', 'action' => 'city', Inflector::slug(strtolower($property['City'])))); ?></td>
<td><?php echo $html->link($property['County'], array('admin' => 0, 'controller' => 'properties', 'action' => 'region', Inflector::slug(strtolower($property['County'])))); ?></td>
<td><?php echo $number->currency($property['Search_Price']); ?></td>
<td><?php echo $time->relativeTime($property['modified']); ?></td>

</tr>
<? } ?>

</table>
<? } ?>
 <? /*
 debug($memberAgent['Property']); */
  ?>