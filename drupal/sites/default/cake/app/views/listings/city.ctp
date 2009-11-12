<h2>Search Result: <?php echo $city['City']['name']; ?></h2>
<p>County: <? echo $html->link($city['County']['name'], array('controller' => 'counties', 'action' => 'view', $city['County']['id'])); ?></p>
<? // debug($city); ?>