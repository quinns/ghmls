<?php
//die(debug($this->viewVars));
echo '<properties>';
echo $xml->serialize($this->viewVars['properties']);
echo '</properties>';