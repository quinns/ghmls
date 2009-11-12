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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<META HTTP-EQUIV="Content-Language" content="EN"> 
	<title>
		<?php __('GreatHomes.org: '); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		if(isset($this->params['prototype'])){
			echo $html->css('lightbox');
			echo $javascript->link('prototype.js');
			echo $javascript->link('scriptaculous.js?load=effects,builder');
			echo $javascript->link('lightbox.js');
		}
		echo $scripts_for_layout;
		if (Configure::read('debug') == 0) { // minified CSS
			$min_css[] = '/modules/acquia/cck/theme/content-module.css';
			$min_css[] = '/modules/acquia/date/date.css';
			$min_css[] = '/modules/acquia/filefield/filefield.css';
			$min_css[] = '/modules/node/node.css';
			$min_css[] = '/modules/system/defaults.css';
			$min_css[] = '/modules/system/system.css';
			$min_css[] = '/modules/user/user.css';
			$min_css[] = '/sites/all/modules/thickbox/thickbox.css';
			$min_css[] = '/sites/all/modules/thickbox/thickbox_ie.css';
			$min_css[] = '/modules/acquia/cck/modules/fieldgroup/fieldgroup.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/html-elements.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/tabs.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/messages.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/block-editing.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/wireframes.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/zen.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/layout-liquid.css';
			$min_css[] = '/sites/greathomes.org/themes/zen/zen/mls.css';
			echo '<!-- minified css -->';	
			echo '<link type="text/css" rel="stylesheet" media="all" href="/min/f=';
			$css_count = 0;
			foreach ($min_css as $value){
				echo $value;
				$css_count ++;
				if($css_count < count($min_css)){
					echo ',';
				}
			}
			echo '" />';
/*
			echo '<link type="text/css" rel="stylesheet" media="print" href="/min/f=/sites/greathomes.org/themes/zen/zen/print.css" />';
*/
			echo '<!-- /minified css -->';
	} else { // non-minified css ?>
		<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/theme/content-module.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/date/date.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/filefield/filefield.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/node/node.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/system/defaults.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system-menus.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/user/user.css" />	
		<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox_ie.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/modules/fieldgroup/fieldgroup.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/html-elements.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/tabs.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/messages.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/block-editing.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/wireframes.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/zen.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/layout-liquid.css" />
		<link type="text/css" rel="stylesheet" media="print" href="/sites/greathomes.org/themes/zen/zen/print.css" />
		<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/mls.css" />
<?php } ?>
<!--[if IE]>
<link type="text/css" rel="stylesheet" media="all" href="/sites/greathomes.org/themes/zen/zen/ie.css" />
<![endif]-->
<script type="text/javascript">
if (parent.frames.length > 0) {
    parent.location.href = self.document.location
}
</script>
<?php
	if(Configure::read('debug') == 0){
		echo '<!-- minified js -->';
		echo '<script type="text/javascript" src="/min/f=mls/webroot/js/jquery-1.3.2.min.js,mls/webroot/js/app_functions.js,misc/jquery.js,misc/drupal.js,sites/all/modules/thickbox/thickbox.js,modules/acquia/google_analytics/googleanalytics.js"></script>';
		echo '<!-- /minified js -->';
	} else {
		echo '<!-- non minified js -->';
		echo '<script type="text/javascript" src="/misc/jquery.js"></script>';
		echo '<script type="text/javascript" src="/misc/drupal.js"></script>';
		echo '<script type="text/javascript" src="/sites/all/modules/thickbox/thickbox.js"></script>';
		echo '<!-- /non minified js -->';
	
	}
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery.extend(Drupal.settings, { "basePath": "/", "googleanalytics": { "trackOutgoing": 1, "trackMailto": 1, "trackDownload": 1, "trackDownloadExtensions": "7z|aac|avi|csv|doc|exe|flv|gif|gz|jpe?g|js|mp(3|4|e?g)|mov|pdf|phps|png|ppt|rar|sit|tar|torrent|txt|wma|wmv|xls|xml|zip" }, "thickbox": { "close": "Close", "next": "Next \x3e", "prev": "\x3c Prev", "esc_key": "or Esc Key", "next_close": "Next / Close on last", "image_count": "Image !current of !total" } });
//--><!]]>
</script>
<?
		if(isset($this->params['map'])){
			//echo $javascript->link("http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAlPjZCqxWs0OAXuFm9YSgmhRg-aVY9BlnwNEvhA8_bKfCb7GClxQaq4A-6B8Wlpvs8JZOWjHwuWZUkw");  
			
			
		//	  echo '    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAlPjZCqxWs0OAXuFm9YSgmhRg-aVY9BlnwNEvhA8_bKfCb7GClxQaq4A-6B8Wlpvs8JZOWjHwuWZUkw&sensor=true" type="text/javascript"></script> ';
           echo $javascript->link('popup.js');
           echo $this->element('map');
           

		}
?>
</head>
<body<?php if (isset($this->params['map'])) { echo ' onload="initialize()" onunload="GUnload()" '; } ?>>
<a name="top"></a>
<div id="page"><div id="page-inner"><a href="/"><img src="/sites/greathomes.org/files/zen_logo.png" alt="Home" id="ghlogo" /></a>
    <div id="header"><div id="header-inner" class="clear-block">
    </div></div> <!-- /#header-inner, /#header -->
    <div id="main"><div id="main-inner" class="clear-block with-navbar">
      <div id="content"><div id="content-inner">
                  <div id="content-top" class="region region-content_top">
            <div id="block-menu-secondary-links" class="block block-menu region-odd even region-count-1 count-2"><div class="block-inner">
  <div class="content">
    <ul class="menu"><li class="leaf first"><a href="/realist-valuation-tool" title="">What is My Home Worth?</a></li>
<li class="leaf"><a href="/mls/find-agent" title="">Find an Agent</a></li>
<li class="leaf"><a href="/mls/search/open_house:1" title="">Open Houses</a></li>
<li class="leaf last"><a href="/mls" title="">Search Homes</a></li>
</ul>  </div>
</div></div> <!-- /block-inner, /block -->
          </div> <!-- /#content-top -->
	<div id="container">
		<div id="mlscontent">
<? /*
		<cake:nocache>
		<?php echo $this->element('user'); ?>
		</cake:nocache>
*/ ?>
		 <div id="spinner" style="display: none; float: left;">
            <?php echo $html->image('/lightbox_images/loading.gif'); ?>
        </div>
		<cake:nocache><?php $session->flash(); ?></cake:nocache>
			<?php echo $content_for_layout; ?>
			<?php 
			
		//	debug($this->params['db_stats'])
			if(isset($this->params['db_stats'])){
				echo $this->element('db_stats');
			}
			 ?>
		</div>
		<div id="footer">
		<?php 
		//if (Configure::read('debug') > 0)  {
			if($this->params['action'] == 'search_result' || $this->params['action'] == 'view' || $this->params['action'] == 'index'){
				echo $html->link('Print this page', '/'.$this->params['url']['url'].'/media:print');
			}
		//} ?>
		</div>
	</div>
	<?php echo $cakeDebug; ?>
	</div></div> <!-- /#content-inner, /#content -->
              <div id="navbar"><div id="navbar-inner" class="clear-block region region-navbar">
          <a name="navigation" id="navigation"></a>
          <div id="block-menu-primary-links" class="block block-menu region-odd odd region-count-1 count-1"><div class="block-inner">
  <div class="content">
    <ul class="menu"><li class="leaf first"><a href="/contact-greathomesorg" title="Contact Us">Contact Us</a></li>
<li class="leaf"><a href="/links" title="More">Links</a></li>
<li class="leaf"><a href="/statistics" title="Statistics">Statistics</a></li>
<li class="leaf last"><a href="/" title="Home">Home</a></li>
</ul>  

</div>
</div></div> <!-- /block-inner, /block -->
        </div></div> <!-- /#navbar-inner, /#navbar -->
    </div></div> <!-- /#main-inner, /#main -->
          <div id="footer"><div id="footer-inner" class="region region-footer">
        <div id="block-menu-menu-footer-nav" class="block block-menu region-odd odd region-count-1 count-3"><div class="block-inner">
  <div class="content">
    <ul class="menu"><li class="leaf first"><a href="/" title="">Home</a></li>
<li class="leaf"><a href="/mls" title="">Search Homes</a></li>
<li class="leaf"><a href="/mls/search/open_house:1" title="">Open Houses</a></li>
<li class="leaf"><a href="/mls/find-agent" title="">Find an Agent</a></li>
<li class="leaf"><a href="/realist-valuation-tool" title="">What is My Home Worth?</a></li>
<li class="leaf"><a href="/statistics" title="">Statistics</a></li>
<li class="leaf"><a href="/links" title="">Links</a></li>
<li class="leaf last"><a href="/contact-greathomesorg" title="">Contact Us</a></li>
</ul>  </div>
</div></div> <!-- /block-inner, /block -->
<div id="block-block-2" class="block block-block region-even even region-count-2 count-4"><div class="block-inner">
  <div class="content">
    <table width="100%" class="ghfooter">
<tr>
<td vlaign="top" align="center">
<img src="/sites/greathomes.org/themes/zen/zen/images/bareis_mls.png" />
</td>
<td vlaign="top">
<p>Copyright &copy; <?php echo date('Y'); ?> Bay Area Real Estate Information Services, Inc. All Rights Reserved</p>
<p>Information on this site is supplied by BAREIS MLS&reg; | <a href="/privacy">Privacy Statement</a></p>

<p id="nudesigns">Web site by <a href="http://nu-designs.com">Nu-Designs Web Marketing, LLC</a></p>
</td>
<td vlaign="top" align="center">
<img src="/sites/greathomes.org/themes/zen/zen/images/norcal_mls.png" />
</td>
</tr>
</table>
  </div>
</div></div> <!-- /block-inner, /block -->
      </div></div> <!-- /#footer-inner, /#footer -->
  </div></div> <!-- /#page-inner, /#page -->
  <script type="text/javascript" src="/modules/acquia/google_analytics/googleanalytics.js"></script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//--><!]]>
</script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
try{var pageTracker = _gat._getTracker("UA-4681539-17");pageTracker._trackPageview();} catch(err) {}
//--><!]]>
</script>
	<?php // echo $this->element('analytics'); ?>
</body>
</html>