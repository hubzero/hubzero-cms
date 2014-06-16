<?php
defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();
$juser =& JFactory::getUser();

$config = JFactory::getConfig();
$juser  = JFactory::getUser();

// Set the generator statement
$this->setGenerator('HUBzero - The open source platform for scientific and educational collaboration');

//do we want to include jQuery
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/hub.js');

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

// Template JS
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/script.js');

// Find out if this is a front page
$app = JFactory::getApplication();
$menu = $app->getMenu();
$isFrontPage = false;
if ($menu->getActive() == $menu->getDefault() && $this->countModules('home-intro')) 
{
	$isFrontPage = true;
	
	// Index page files only
	//$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/index.js');
}

// Set page title
$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());

// Get rid of introduction.css
$document = JFactory::getDocument();
$data = $document->getHeadData();
$nd = array();

foreach ($data['styleSheets'] as $key => $val)
{
	if (!strstr($key, '/media/system/css/introduction.css'))
	{
		$nd[$key] = $val;
	}
}
$data['styleSheets'] = $nd;

$document->setHeadData($data);

?>
<!DOCTYPE html>
<html>
<head>

	<meta name="viewport" content="width=device-width">
    
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'tooltip', 'comments', 'voting', 'icons', 'buttons', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
	
	<jdoc:include type="head" />
	
</head>
<body<?php if ($isFrontPage) : echo ' id="frontpage"'; endif; ?>>
	
	<jdoc:include type="modules" name="notices" />
	<jdoc:include type="modules" name="helppane" />
	
		<header id="page-header">
			<section class="top-wrapper cf">
			
				<div id="top" class="cf">
					<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>" class="logo">
						<p><?php echo $config->getValue('config.sitename'); ?></p>
						<p class="tagline hide-m">HUBzero Baselayer template</p>
					</a>
					
					<div id="mobile-nav" class="show-m">
						<ul>
							<li><a id="mobile-menu"><span>Menu</span></a></li>
							<li><a id="mobile-search"><span>Search</span></a></li>
						</ul>
					</div>
				</div>
				
				<div id="search-box">
					<jdoc:include type="modules" name="search" />
				</div>
			</section>
			
			<nav id="main-navigation" class="">
			
				<div class="wrapper cf">
				
					<div id="account">
					<?php if (!$juser->get('guest')) { 
							$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
					?>
						<ul class="menu cf <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li>
								<div id="account-info">
									<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($profile); ?>" alt="<?php echo $juser->get('name'); ?>" />
									<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
										<?php echo stripslashes($juser->get('name')); ?> 
										<span class="account-email"><?php echo $juser->get('email'); ?></span>
									</a>
									
									<p class="account-logout">
										<a href="<?php echo JRoute::_('index.php?option=com_logout'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_LOGOUT'); ?></span></a>
									</p>
								</div>
								<ul>
									<li id="account-dashboard">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_DASHBOARD'); ?></span></a>
									</li>
									<li id="account-profile">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_PROFILE'); ?></span></a>
									</li>
									<li id="account-messages">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_ACCOUNT_MESSAGES'); ?></span></a>
									</li>
									<li id="account-logout">
										<a href="<?php echo JRoute::_('index.php?option=com_logout'); ?>"><span><?php echo JText::_('TPL_HUBBASIC_LOGOUT'); ?></span></a>
									</li>
								</ul>
							</li>
						</ul>
					<?php } else { ?>
						<ul class="menu <?php echo (!$juser->get('guest')) ? 'loggedin' : 'loggedout'; ?>">
							<li id="account-login">
								<?php $login_route = (version_compare(JVERSION, '2.5', 'ge')) ? 'index.php?option=com_users&view=login' : 'index.php?option=com_user&view=login'; ?>
								<a href="<?php echo JRoute::_($login_route); ?>" title="<?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?>"><?php echo JText::_('TPL_HUBBASIC_LOGIN'); ?></a>
							</li>
							<li id="account-register">
								<a href="<?php echo JRoute::_('index.php?option=com_register'); ?>" title="<?php echo JText::_('TPL_HUBBASIC_SIGN_UP'); ?>"><?php echo JText::_('TPL_HUBBASIC_REGISTER'); ?></a>
							</li>
						</ul>
						<?php /* <jdoc:include type="modules" name="account" /> */ ?>
					<?php } ?>
					</div><!-- / #account -->
					
					<div id="main-nav"><jdoc:include type="modules" name="user3" /></div>
					
				</div><!-- / #wrapper -->
			</nav>
			
			<?php if (!$isFrontPage) : ?>
			
			<div id="trail">
				<jdoc:include type="modules" name="breadcrumbs" />				
			</div><!-- / #trail -->
			
			<!--div id="main-heading" class="append-after">
				<h1>This is a heading</h1>
			</div-->
			
			<?php endif; ?>
			
		</header>
		
		<?php
		// home intro 
		if ($this->countModules('home-intro')) : ?>
		
			<div id="home-intro">
				<jdoc:include type="modules" name="home-intro" />
			</div>
			
		<?php endif; ?>
		
		<div id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
			
			<a id="content-anchor"></a>
			<?php if ($this->countModules('left')) : ?>
				<div class="main section withleft cf">
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
						<!-- start component output -->
						<?php 
						
						if (JRequest::getVar('option', '') == 'com_content')
						{
							echo '<div class="section-inner">';						
						}
						
						?>
						
						<jdoc:include type="component" />
						
						<?php 
						
						if (JRequest::getVar('option', '') == 'com_content')
						{
							echo '</div>';						
						}
						?>
						
						<!-- end component output -->
			<?php if ($this->countModules('left or right')) : ?>
					</div><!-- / .subject -->
				</div><!-- / .main section -->
			<?php endif; ?>
			
			
		</div><!-- / #content -->

		<div id="footer">
			<div class="wrapper">
				<jdoc:include type="modules" name="footer" />
				
				<div id="hubzero-proud-branding">
					<p>This hub is lovingly crafted and proudly powered by the <a href="http://www.hubzero.org" target="_blank">HUBzero platform</a>.</p>
				</div>
				
			</div>
		</div><!-- / #footer -->
		
	
		<jdoc:include type="modules" name="endpage" />
	
</body>
</html>