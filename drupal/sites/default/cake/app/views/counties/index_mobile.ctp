<div id="ajax">
        <div class="toolbar">
            <h1>Counties</h1>
            <a class="back" href="#home">Back</a>
        </div>
        <div class="info">
            <p>Browse properties by county name.</p>
        </div>
        <ul>
		<?
		foreach($counties as $key => $value){
			echo '<li class="arrow">';
			echo $html->link($value, array('controller' => 'properties', 'action' => 'region', Inflector::slug(strtolower($value)), 'theme:mobile'));
			echo '<small class="counter">'.$number->format($summary[$key]).'</small>';
			echo '</li>';
		}
		?>
        </ul>
<?php echo $this->element('footer_mobile'); ?>
</div>
