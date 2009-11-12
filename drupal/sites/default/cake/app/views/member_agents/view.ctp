<style>
dl {
	line-height: 2em;
	margin: 0em 0em;
	/* width: 60%; */
}
dl.altrow {
	background: #f4f4f4;
}
dt {
	font-weight: bold;
	padding-left: 4px;
	vertical-align: top;
}
dd {
	margin-left: 200px;
	margin-top: -2em;
	vertical-align: top;
}
</style>

<?php
	$this->pageTitle = 'Agent Detail: '.$memberAgent['MemberAgent']['Agent_First_Name'].' '.$memberAgent['MemberAgent']['Agent_Last_Name'];
?>
<h2><?php echo $this->pageTitle; ?></h2>

<div >
	<dl><?php $i = 0; $class = ' class="altrow"';?>
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
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberAgent['MemberAgent']['Agent_Phone_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Email Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php
/* 			 echo $text->autoLink($memberAgent['MemberAgent']['Agent_Email_Address']);  */
						$form_url = 'http://'.$_SERVER['SERVER_NAME'].'/contact-agent';
						$item = $form->create('MemberAgent', array('url' => $form_url));
						$item = '<form method="GET" action="/contact-agent">';
						$item.= '<input type="hidden" name="recipient_name" value="'.$memberAgent['MemberAgent']['Agent_First_Name'].' '.$memberAgent['MemberAgent']['Agent_Last_Name'].'">';						
						$recipient_address = $memberAgent['MemberAgent']['Agent_Email_Address'];
						$item.= '<input type="hidden" name="recipient_email" value="'.$memberAgent['MemberAgent']['Agent_Email_Address'].'">';
/* 						$item.= $form->end('Click to Send Mail to '.$property['Property']['Agent_Name']);										 */
						$item.= $form->end('Click to Contact Agent');										
						echo $item;
			 ?>
<!-- 			&nbsp; -->
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent Web Page Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $text->autoLink($memberAgent['MemberAgent']['Agent_Web_Page_Address']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agent License'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo  $memberAgent['MemberAgent']['Agent_License']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Listing Office'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php 
			if(isset($memberAgent['MemberOffice']['Office_Long_Name'])){
				echo  $html->link($memberAgent['MemberOffice']['Office_Long_Name'], array('controller' => 'member_offices', 'action' => 'view', $memberAgent['MemberAgent']['Office_MLS_ID'])); 
			}
			?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $time->relativeTime($memberAgent['MemberAgent']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>

<?php if (!empty($memberAgent['Property'])) { ?>
<h2>Properties Represented by this Agent</h2>
<table class="ghresultstable" width="100%">
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
<? } else {
	echo '<h3>This agent does not currently list any active properties in our system.</h3>';

} ?>
 <? /*
 debug($memberAgent['Property']); */
/*  debug($memberAgent); */
/* echo $html->link('See Properties Listed by this Agent', array('controller' => 'properties', 'action' => 'listing_agent', $memberAgent['MemberAgent']['Agent_MLS_ID'])); */
  ?>