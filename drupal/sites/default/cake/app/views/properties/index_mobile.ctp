<?
$paginator->options(array('url' => $this->passedArgs, 'update' => 'index'));
?>
<div id="ajax">
        <div class="toolbar">
            <h1><?php echo $properties[0]['Property']['County']; ?></h1>
            <a class="back" href="#home">Back</a>
        </div>
		<div class="info">
			<? echo $paginator->counter(array('format' => __('Page %page% of %pages%', true))); ?>
		</div>
        <div id="index">
        <ul>
		<?
/* 		debug($properties[0]); */
		foreach($properties as $key => $value){
			echo '<li class="arrow">';
			$link =  $html->image(array('admin' => 0, 'controller' => 'properties', 'action' => 'image', $value['Property']['ML_Number_Display'].'/thumb'), array('align' => 'left', 'width' => 130, 'height' => 100));
			$link .= '&nbsp;'.$value['Property']['City'];
			$link .= '<br />&nbsp;'.$number->currency($value['Property']['Search_Price']);
			$link .= '<br />&nbsp;'.$value['PropertyType']['name'];
			echo $html->link($link, array('controller' => 'properties', 'action' => 'view', $value['Property']['ML_Number_Display'], 'theme:mobile'), null, null, false);
			echo '</li>';
		}
		?>
        </ul>
        </div>
        <?
/*
        		debug($properties[0]);
*/
        ?>

<div id="pagination">

        <ul class="individual">
	<li><?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?></li>
	<li><?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?></li>
        </ul>
        
        </div>

<div class="info">
<? echo $paginator->counter(array('format' => __('Page %page% of %pages%', true))); ?>
</div>


<?php echo $this->element('footer_mobile'); ?>


</div>
