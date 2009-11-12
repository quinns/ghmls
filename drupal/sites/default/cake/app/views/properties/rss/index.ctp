<?php

foreach ($properties as $property) {
        $postTime = strtotime($property['Property']['modified']);
 
        $postLink = array(
            'controller' => 'properties',
            'action' => 'view',
            $property['Property']['ML_Number_Display']);
        // You should import Sanitize
        App::import('Sanitize');
        // This is the part where we clean the body text for output as the description 
        // of the rss item, this needs to have only text to make sure the feed validates
        $bodyText = preg_replace('=\(.*?\)=is', '', $property['Property']['Marketing_Remarks']);
        $bodyText = $text->stripLinks($bodyText);
        $bodyText = Sanitize::stripAll($bodyText);
        $bodyText = $text->truncate($bodyText, 400, '...', true, true);
 		$mls_id = $property['Property']['ML_Number_Display'];
 		$image = $html->image('http://greathomes.org/mls/properties/image/'.$mls_id.'/thumb/image:thumb_'.$mls_id.'.jpg');
 		$image_link = $html->link($image, array('controller' => 'properties', 'action' => 'view', $mls_id), null, null, false);
 		
        echo  $rss->item(array(), array(
            'title' => $property['Property']['Street_Full_Address'],
            'link' => $postLink,
            'guid' => array('url' => $postLink, 'isPermaLink' => 'true'),
            'description' =>  $image_link.'<br />'.$bodyText,
           // 'dc:creator' => $property['Property']['author'],
            'pubDate' => $property['Property']['created']));
    }
