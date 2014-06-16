<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

ximport('Hubzero_Module_Helper');
ximport('Hubzero_Document');

$config =& JFactory::getConfig();
$juser =& JFactory::getUser();

$this->template = 'baselayer';

$this->setTitle($config->getValue('config.sitename') . ' - ' . $this->getTitle());

ximport('Hubzero_Browser');
$browser = new Hubzero_Browser();
$b = $browser->getBrowser();
$v = $browser->getBrowserMajorVersion();

?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width">
	
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Hubzero_Document::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'comments', 'voting', 'layout', 'core')); /* reset MUST come before all others except fontcons */ ?>" />
	
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/html/mod_reportproblems/mod_reportproblems.css" />
		
	<?php if (JPluginHelper::isEnabled('system', 'jquery')) { ?>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.ui.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.fancybox.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/jquery.tools.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.jquery.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.jquery.js"></script>
<?php } else { ?>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/media/system/js/mootools.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/hub.js"></script>
		<script type="text/javascript" src="<?php echo $this->baseurl; ?>/modules/mod_reportproblems/mod_reportproblems.js"></script>
<?php } ?>

	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/script.js"></script>
		
</head>

<body>
	<?php Hubzero_Module_Helper::displayModules('notices'); ?>
	<?php Hubzero_Module_Helper::displayModules('helppane'); ?>
	
	<header id="page-header">
		<section class="top-wrapper cf">
		
			<div id="top" class="cf">
				<a href="<?php echo $this->baseurl; ?>" title="<?php echo $config->getValue('config.sitename'); ?>" class="logo">
					<p><?php echo $config->getValue('config.sitename'); ?></p>
					<p class="tagline hide-m">HUBzero template</p>
				</a>
				
				<div id="mobile-nav" class="show-m">
					<ul>
						<li><a id="mobile-menu"><span>Menu</span></a></li>
						<li><a id="mobile-search"><span>Search</span></a></li>
					</ul>
				</div>
			</div>
			
			<div id="search-box">
				<?php Hubzero_Module_Helper::displayModules('search'); ?>
			</div>
		</section>
		
		<nav id="main-navigation" class="hide-m">
		
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
				
				<div id="main-nav"><?php Hubzero_Module_Helper::displayModules('user3'); ?></div>
				
			</div><!-- / #wrapper -->
		</nav>
		
		<div id="trail">
			<div class="breadcrumbs">
				<a href="/" title="<?php echo $config->getValue('config.sitename'); ?>">Home</a>
				
				<?php
					echo ' → <span>' . $this->error->getMessage() . '</span>';
				?>
			</div>
		</div>
		
		<!--div id="main-heading" class="append-after">
			<h1>This is a heading</h1>
		</div-->

	</header>
	
	
	<div id="content" class="<?php echo JRequest::getVar('option', ''); ?>" role="main">
			
		<a id="content-anchor"></a>
		
		<div class="section-inner">		
			<div class="grid">
					
				<div class="col span6 error-code">
					<?php echo (in_array($this->error->getCode(), array(404, 403, 500))) ? $this->error->getCode() : 500; ?>
				</div>
			
				<div class="col span6 omega">
					<h2><?php echo $this->error->getMessage() ?></h2>
				
					<p><?php echo JText::_('You may not be able to visit this page because of:'); ?></p>
		
					<ol>
		<?php if ($this->error->getCode() != 403) { ?>
						<li><?php echo JText::_('An out-of-date bookmark/favourite.'); ?></li>
						<li><?php echo JText::_('A search engine that has an out-of-date listing for this site.'); ?></li>
						<li><?php echo JText::_('A mis-typed address.'); ?></li>
						<li><?php echo JText::_('The requested resource was not found.'); ?></li>
		<?php } ?>
						<li><?php echo JText::_('This page may belong to a group with restricted access.  Only members of the group can view the contents.'); ?></li>
						<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
					</ol>
		<?php if ($this->error->getCode() != 403) { ?>
					<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
		<?php } else { ?>
					<p><?php echo JText::_('If difficulties persist and you feel that you should have access to the page, please file a trouble report by clicking on the Help! option on the menu above.'); ?></p>
		<?php } ?>
				</div>
			</div>	
		</div>					
	</div>
	
	
	<div id="footer">
		<div class="wrapper">
			<?php Hubzero_Module_Helper::displayModules('footer'); ?>
			
			<div id="hubzero-proud-branding">
				<p>This hub is lovingly crafted and proudly powered by the <a href="http://www.hubzero.org" target="_blank">HUBzero platform</a>.</p>
			</div>
			
		</div>
	</div><!-- / #footer -->
	
	<jdoc:include type="modules" name="endpage" />
	
	
</body>
</html>