<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

ximport('xmodule');

$config =& JFactory::getConfig();
$juser =& JFactory::getUser();

?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<link rel="stylesheet" href="/templates/fresh/css/main.css" type="text/css" />
	<link rel="stylesheet" href="/templates/fresh/css/error.css" type="text/css" />
	<link rel="stylesheet" href="/templates/fresh/html/mod_reportproblems/mod_reportproblems.css" type="text/css" />
	
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl ?>/templates/fresh/css/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/dummy.css" id="dummy_css" />
	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/ie7win.css" />
	<![endif]-->
	<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/ie6win.css" />
	<![endif]-->
	<script type="text/javascript" src="/media/system/js/mootools.js"></script>
	<script type="text/javascript" src="/templates/fresh/js/hub.js"></script>
	<script type="text/javascript" src="/modules/mod_reportproblems/mod_reportproblems.js"></script>
</head>	 
<body>
	
	<div id="mainwrap">
    <div id="uc"><div>Beta 3.0</div></div>
<?php XModuleHelper::displayModules('notices'); ?>
		<div id="header">
             <div id="headerwrap">
                <h1><a href="/home" title="<?php echo $config->getValue('config.sitename'); ?>"><?php echo $config->getValue('config.sitename'); ?></a></h1>			
                <ul id="toolbar" class="<?php if (!$juser->get('guest')) { echo 'loggedin'; } else { echo 'loggedout'; } ?>">
        <?php
        if (!$juser->get('guest')) {
        
            // Find the user's most recent support tickets
            ximport('xmessage');
            $database =& JFactory::getDBO();
            $recipient = new XMessageRecipient( $database );
            $rows = $recipient->getUnreadMessages( $juser->get('id'), 0 );
            echo "\t\t\t".'<li id="username"><a href="/members/'.$juser->get('id').'" title="My Account">'.$juser->get('name').' ('.$juser->get('username').')</a></li>'."\n";
           echo "\t\t\t".'<li id="usermessages"><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=inbox').'">'.count($rows).' New Messages</a></li>'."\n";
            echo "\t\t\t".'<li id="logout"><a href="/logout" title="Logout"><span>Logout</span></a></li>'."\n";
            echo "\t\t\t".'<li id="myaccount"><a href="/members/'.$juser->get('id').'" title="My Account"><span>My Account</span></a></li>'."\n";
            echo "\t\t\t".'<li id="myhub"><a href="/myhub" title="My neesHUB"><span>My NEEShub</span></a></li>'."\n";           
        } else {
            echo "\t\t\t".'<li id="login"><a href="/login" title="Login">Login</a></li>'."\n";
            echo "\t\t\t".'<li id="register"><a href="/register" title="Sign up for a free account">Register</a></li>'."\n";
        }
        echo "\t\t\t".'<li id="aboutsite"><a href="/about" title="About NEES"><span>About NEES</span></a></li>'."\n";
        echo "\t\t\t".'<li id="sitemap"><a href="/xmap" title="Sitemap"><span>Sitemap</span></a></li>'."\n";
        echo "\t\t\t".'<li id="feedback"><a href="/feedback" title="Feedback"><span>Feedback</span></a></li>'."\n";        
        ?>
                </ul>			
                <?php XModuleHelper::displayModules('search'); ?>

                <p id="tab"><a href="/support/" title="Need help? Send a trouble report to our support team."><span>Support</span></a></p>
             </div>
             
            <div id="nav">
                <h2>Navigation</h2>
 				<?php XModuleHelper::displayModules('user3'); ?>
				<?php XModuleHelper::displayModules('introblock'); ?>
                <div class="clear"></div>
            </div><!-- / #nav -->
		</div><!-- / #header -->
	
		<?php XModuleHelper::displayModules('helppane'); ?>
		<div id="afterclear">&nbsp;</div>

		<div id="trail">
			You are here: <?php
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		$items = $pathway->getPathWay();
		$l = array();
		$l[] = '<a href="/home" class="home">Home</a>';
		
		foreach ($items as $item) 
		{
			$text = trim(stripslashes($item->name));
			if (strlen($text) > 50) {
				$text = $text.' ';
				$text = substr($text,0,50);
				$text = substr($text,0,strrpos($text,' '));
				$text = $text.' &#8230;';
			}
			$url = JRoute::_($item->link);
			$url = str_replace('%20','+',$url);
			$l[] = '<a href="'.$url.'">'.$text.'</a>';
		}
		echo implode(' &raquo; ',$l);
		?>
		</div>
	
	<!--  EOT SECTION SPECIFIC BANNER -->
		
	  <div id="wrap">
		<div id="content" class="<?php echo $option; ?>">

				<div id="outline">
					<div id="errorbox" class="code-<?php echo $this->error->code ?>">
						<h2><?php echo $this->error->message ?></h2>

						<p><?php echo JText::_('You may not be able to visit this page because of:'); ?></p>

						<ol>
	<?php if ($this->error->code != 403) { ?>
							<li><?php echo JText::_('An out-of-date bookmark/favourite.'); ?></li>
							<li><?php echo JText::_('A search engine that has an out-of-date listing for this site.'); ?></li>
							<li><?php echo JText::_('A mis-typed address.'); ?></li>
							<li><?php echo JText::_('The requested resource was not found.'); ?></li>
	<?php } ?>
							<li><?php echo JText::_('You have no access to this page.'); ?></li>
							<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
						</ol>

						<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
					</div>

					<form method="get" action="/search">
						<fieldset>
							<?php echo JText::_('Please try the'); ?> <a href="/index.php" title="<?php echo JText::_('Go to the home page'); ?>"><?php echo JText::_('Home Page'); ?></a> <span><?php echo JText::_('or'); ?></span> 
							<label>
								<?php echo JText::_('Search:'); ?> 
								<input type="text" name="searchword" value="" />
							</label>
							<input type="submit" value="<?php echo JText::_('Go'); ?>" />
						</fieldset>
					</form>
				</div>
	<?php 
				if ($this->debug || $juser->get('username') == 'nkissebe' || $juser->get('username') == 'zooley') :
					echo "\t\t".'<div id="techinfo">'."\n";
					echo $this->renderBacktrace()."\n";
					echo "\t\t".'</div>'."\n";
				endif;
	?>
		</div><!-- / #content -->
		
		<div id="footer">
		    <?php XModuleHelper::displayModules('footer'); ?>
		</div><!-- / #footer -->
	  </div><!-- / #wrap -->
	 </div> <!--  #mainwrap -->	
	 
	 
	 <!--  Google Analytics Begin -->	
	 <script type="text/javascript">
         var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
         document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
     </script>
     
	 <script type="text/javascript">
         try{
              var pageTracker = _gat._getTracker("UA-xxxxxx-x");
              pageTracker._trackPageview();
         } catch(err) {}
     </script>
	 
	<!--  Google Analytics End -->	
	 
	</body>
</html>
<?php
$title = $this->getTitle();
$this->setTitle( $config->getValue('config.sitename').' - '.$title );
?>