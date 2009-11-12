<?php
/* SVN FILE: $Id: default.ctp 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- drake.ctp -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('GreatHomes.org: '); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
	/*
		echo $html->css('cake.generic');
		echo $html->css('styles');
	*/
		echo $html->css('lightbox');
		echo $javascript->link('prototype.js');
		echo $javascript->link('scriptaculous.js?load=effects,builder');
		echo $javascript->link('lightbox.js');
	
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
	<? /*
		<div id="header">
			<h1><?php echo $html->link(__('GreatHomes.org Development Site', true), '/'); ?></h1>
		</div>
	*/ ?>	
		<div id="content">
		<?php $session->flash(); ?>
		 <div id="spinner" style="display: none; float: right;">
            <?php echo $html->image('/lightbox_images/loading.gif'); ?>
        </div>
        <!-- drake.ctp -->
			<?php echo $content_for_layout; ?>
		</div>
		<div id="footer"></div>
	</div>
	<?php echo $cakeDebug; ?>
	<?php echo $this->element('analytics'); ?>
</body>
</html>