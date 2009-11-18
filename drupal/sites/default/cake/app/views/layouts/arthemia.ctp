<?
if(isset($this->params['domain_info']['this_client']['client_name'])){
	$app_name = $this->params['domain_info']['this_client']['client_name'];
} else {
	$app_name = APP_NAME;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php __($app_name); ?> : 
		<?php echo $title_for_layout; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     	<?php
		echo $scripts_for_layout;
	?>
    <link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/theme/content-module.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/date/date.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/node/node.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/system/defaults.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system-menus.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/user/user.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/ctools/css/ctools.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/domain/domain_nav/domain_nav.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/extlink/extlink.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/filefield/filefield.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/logintoboggan/logintoboggan.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox_ie.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/modules/fieldgroup/fieldgroup.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/default/themes/arthemia/style.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/default/themes/arthemia/ribbon.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/default/themes/arthemia/aggregator.css?p" />
    <script type="text/javascript" src="/misc/jquery.js?p"></script>
<script type="text/javascript" src="/misc/drupal.js?p"></script>
<script type="text/javascript" src="/sites/all/modules/extlink/extlink.js?p"></script>
<script type="text/javascript" src="/sites/all/modules/thickbox/thickbox.js?p"></script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery.extend(Drupal.settings, { "basePath": "/", "extlink": { "extTarget": 0, "extClass": "ext", "extSubdomains": 1, "extExclude": "", "extInclude": "", "extAlert": 0, "extAlertText": "This link will take you to an external web site. We are not responsible for their content.", "mailtoClass": "mailto" }, "thickbox": { "close": "Close", "next": "Next \x3e", "prev": "\x3c Prev", "esc_key": "or Esc Key", "next_close": "Next / Close on last", "image_count": "Image !current of !total" } });
//--><!]]>
</script>
  </head>
  <body>
    <div id="head" class="clearfloat">

      <div class="clearfloat">
        <div id="logo">
          <a href="/" title="<?php echo $app_name; ?>"><span id="sitename"><?php echo $app_name; ?></span></a>                </div>

              </div>

      <div id="navbar" class="clearfloat">
          <div id="page-bar"></div>              </div>
    </div>

    <div id="page" class="clearfloat">

            
      
      <div id="content" class="main-content with-sidebar">
        
<!-- CONTENT -->
        			<?php $session->flash(); ?>

			<?php echo $content_for_layout; ?>
      </div>

              <div id="sidebar">
          <div id="block-user-1" class="block block-user odd">
          <? echo $this->element('properties_menu', array('client_data' => $client_data)); ?>
  <h3>Navigation</h3>

  <div class="content">
    <ul class="menu"><li class="leaf last"><a href="/tracker">Recent posts</a></li>
</ul>  </div>
</div>
        </div>
      
          </div>

    <div id="footer-region" class="clearfloat">
      <div id="footer-left" class="clearfloat">
              </div> 		

      <div id="footer-middle" class="clearfloat">
              </div>

      <div id="footer-right" class="clearfloat">
              </div>
    </div>

    <div id="footer-message">
<!--
      <a href="http://drupal.org/project/arthemia">Arthemia</a> is based on the original design by <a href="http://michaelhutagalung.com">Michael Hutagalung</a>.
-->
          </div>
      </body>
</html>