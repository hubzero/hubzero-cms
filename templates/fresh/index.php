<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
$config =& JFactory::getConfig();
$juser =& JFactory::getUser();

?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->baseurl ?>/templates/fresh/css/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/dummy.css" id="dummy_css" />
	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/ie7win.css" />
	<![endif]-->
	<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/ie6win.css" />
	<![endif]-->
		<script type="text/javascript" src="media/system/js/jquery-1.4.2.js"></script>
		<script type="text/javascript">var $jQ = jQuery.noConflict();</script> 	
		<script type="text/javascript" src="site/fancybox/jquery.fancybox-1.3.1.pack.js"></script>	
		<link rel="stylesheet" href="site/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
	
	
<?php if ($this->countModules( 'banner or welcome or academybanner' )) { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/css/home.css" id="home_css" />
<?php } ?>	
	 </head>	 
	<body<?php if ($this->countModules( 'banner or welcome' )) : echo ' id="frontpage"'; endif; ?>>
	
	<div id="mainwrap">
   
	<jdoc:include type="modules" name="notices" />
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
                <jdoc:include type="modules" name="search" />
        <?php if ($this->countModules( 'helppane' )) : ?>
                <p id="tab"><a href="/support/" title="Need help? Send a trouble report to our support team."><span>Support</span></a></p>
        <?php endif; ?>
             </div>
             
            <div id="nav">
                <h2>Navigation</h2>
                <jdoc:include type="modules" name="user3" />
                <jdoc:include type="modules" name="introblock" />
                <div class="clear"></div>
            </div><!-- / #nav -->
		</div><!-- / #header -->
	
		<jdoc:include type="modules" name="helppane" />
		<div id="afterclear">&nbsp;</div>
	
	<?php if ($this->countModules( 'banner or welcome' )) : ?>
		<div id="home-splash">	    
			<div id="features-wrap">
				<div id="features">
	<?php if ($this->countModules( 'banner' )) : ?>
					<jdoc:include type="modules" name="banner" />
	<?php else : ?>
					
	<?php endif; ?>
				</div><!-- / #features -->
	            <?php if ($this->countModules( 'spotlight' )) : ?>
	            <div id="spotlight">
                	<h3>In the Spotlight</h3>
	             <jdoc:include type="modules" name="spotlight" />            
	               <?php if ($this->countModules( 'rightspot' )) : ?>
	                <div id="rightspot">
	                 <jdoc:include type="modules" name="rightspot" />
	                </div><!-- / #rightspot -->
	                 <?php endif; ?>
	            </div><!-- / #spotlight -->          
	            <?php endif; ?>
			</div><!-- / #features-wrap -->
			<?php if ($this->countModules( 'welcome' )) : ?>
			<div id="welcome">
				<jdoc:include type="modules" name="welcome" />
			</div><!-- / #welcome -->
	<?php endif; ?>
		</div><!-- / #home-splash -->
	    <div class="clear"></div>
	<?php endif; ?>
	<?php if (!$this->countModules( 'banner or welcome' )) : ?>
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
	<?php endif; ?>
	
	<!--  EOT SECTION SPECIFIC BANNER -->
<?php if ($this->countModules( 'academybanner' )) : ?>
		 <div id="aca_b_trail"> 
			<jdoc:include type="modules" name="academybanner" />
		 </div> <!--/ #academy header-right -->
<?php endif; ?>
		
	  <div id="wrap">
		<div id="content" class="<?php echo $option; ?>">
	<?php if ($this->countModules( 'left' )) : ?>
			<div class="main section withleft">
			<div class="aside">
				<jdoc:include type="modules" name="left" />
			</div><!-- / #column-left -->
			<div class="subject">
	<?php endif; ?>
	<?php if ($this->countModules('right')) : ?>
			<div class="main section">
			<div class="aside">
				<jdoc:include type="modules" name="right" />
			</div><!-- / .aside -->
			<div class="subject">
	<?php endif; ?>
				<!-- innerwrap is used to fix some IE 6 display bugs -->
				<div class="innerwrap<?php if ($this->countModules('banner or welcome')) : echo ' frontpage'; endif; ?>">
					<jdoc:include type="component" />
				</div><!-- / .innerwrap -->
	<?php if ($this->countModules('left or right')) : ?>
			</div><!-- / .subject -->
			<div class="clear"></div>
			</div><!-- / .main section -->
	<?php endif; ?>
		</div><!-- / #content -->
		
		<div id="footer">
		    <jdoc:include type="modules" name="footer" />
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
              var pageTracker = _gat._getTracker("UA-6278809-4");
              pageTracker._trackPageview();
         } catch(err) {}
     </script>
	 
	<!--  Google Analytics End -->	
	 
	</body>
</html>
	<?php
	$xhub =& XFactory::getHub();
	$title = $this->getTitle();
	$this->setTitle( $xhub->getCfg('hubShortName').' - '.$title );
?>