<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$browser = new \Hubzero\Browser\Detector();
$b = $browser->name();
$v = $browser->major();

$config = JFactory::getConfig();
$juser  = JFactory::getUser();

// return url
$return = DS . trim(str_replace(JURI::base(),'', JURI::current()), DS);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo $b . ' ' . $b . $v; ?>"> <!--<![endif]-->
	<head>
		<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> Doesn't validate... -->

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(array('fontcons', 'reset', 'columns', 'notifications', 'pagination', 'tabs', 'tags', 'comments', 'voting', 'layout')); /* reset MUST come before all others except fontcons */ ?>" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/hubbasic2013/css/main.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/group.css" type="text/css" />

		<jdoc:include type="head" />
	</head>
	<body class="contentpane" id="group-body">
		<jdoc:include type="modules" name="notices" />
		<jdoc:include type="modules" name="helppane" />

		<div class="super-group-bar">
			<div class="grid">
				<div class="col span6">
					<a href="<?php echo $this->baseurl; ?>/" class="poweredby">
						powered by <span><?php echo $config->getValue('sitename'); ?></span>
					</a>
				</div>
				<div class="col span6 omega">
					<div id="account" role="navigation">
						<?php if (!$juser->get('guest')) : ?>
							<?php $profile = \Hubzero\User\Profile::getInstance($juser->get('id')); ?>
							<ul class="menu loggedin">
								<li>
									<div id="account-info">
										<a class="account-details" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>">
											<img src="<?php echo $profile->getPicture(); ?>" alt="<?php echo $juser->get('name'); ?>" />
											<span class="account-name"><?php echo stripslashes($profile->get('name')); ?></span>
											<span class="account-email"><?php echo $profile->get('email'); ?></span>
										</a>
									</div>
									<ul>
										<li id="account-dashboard">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_DASHBOARD'); ?></span>
											</a>
										</li>
										<li id="account-profile">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=profile'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_PROFILE'); ?></span>
											</a>
										</li>
										<li id="account-messages">
											<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=messages'); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_ACCOUNT_MESSAGES'); ?></span>
											</a>
										</li>
										<li id="account-logout">
											<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout&return=' . base64_encode($return)); ?>">
												<span><?php echo JText::_('TPL_SYSTEM_LOGOUT'); ?></span>
											</a>
										</li>
									</ul>
								</li>
							</ul>
						<?php else : ?>
							<ul class="menu loggedout">
								<?php if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') != '0') : ?>
									<li id="account-register">
										<a href="<?php echo JRoute::_('index.php?option=com_register&return=' . base64_encode($return)); ?>" title="<?php echo JText::_('TPL_SYSTEM_SIGN_UP'); ?>">
											<?php echo JText::_('TPL_SYSTEM_REGISTER'); ?>
										</a>
									</li>
								<?php endif; ?>
								<li id="account-login">
									<a href="<?php echo JRoute::_('index.php?option=com_groups&cn='.JRequest::getCmd('cn','').'&task=login&return=' . base64_encode($return)); ?>" title="<?php echo JText::_('TPL_SYSTEM_LOGIN'); ?>">
										<?php echo JText::_('TPL_SYSTEM_LOGIN'); ?>
									</a>
								</li>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<jdoc:include type="message" />
		<jdoc:include type="component" />
		<jdoc:include type="modules" name="endpage" />
	</body>
</html>