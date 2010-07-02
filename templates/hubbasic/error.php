<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
$config =& JFactory::getConfig();

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $config->getValue('config.sitename'); ?> - <?php echo $this->title; ?> - <?php echo $this->error->message ?></title>
	<link rel="stylesheet" media="all" href="<?php echo $this->baseurl ?>/templates/hubbasic/css/error.css" type="text/css" />
</head>
<body>
	<div id="wrap">
		<div id="header">
			<h1><a href="<?php echo $this->baseurl ?>" title="<?php echo $config->getValue('config.sitename'); ?>"><?php echo $config->getValue('config.sitename'); ?></a></h1>
		</div>
		<div id="outline">
			<div id="errorbox" class="code-<?php echo $this->error->code ?>">
				<h2><?php echo $this->error->code ?> - <?php echo $this->error->message ?></h2>
				
				<p><?php echo JText::_('You may not be able to visit this page because of:'); ?></p>
				
				<ol>
					<li><?php echo JText::_('An out-of-date bookmark/favourite'); ?></li>
					<li><?php echo JText::_('A search engine that has an out-of-date listing for this site'); ?></li>
					<li><?php echo JText::_('A mis-typed address'); ?></li>
					<li><?php echo JText::_('You have no access to this page'); ?></li>
					<li><?php echo JText::_('The requested resource was not found'); ?></li>
					<li><?php echo JText::_('An error has occurred while processing your request.'); ?></li>
				</ol>
				
				<p><?php echo JText::_('If difficulties persist, please contact the system administrator of this site.'); ?></p>
			</div>
			
			<form method="get" action="/search">
				<fieldset>
					<?php echo JText::_('Please try the'); ?> <a href="index.php" title="<?php echo JText::_('Go to the home page'); ?>"><?php echo JText::_('Home Page'); ?></a> <span><?php echo JText::_('or'); ?></span> 
					<label>
						<?php echo JText::_('Search:'); ?> 
						<input type="text" name="searchword" value="" />
					</label>
					<input type="submit" value="<?php echo JText::_('Go'); ?>" />
				</fieldset>
			</form>
		</div>
<?php 
		if ($this->debug) :
			echo "\t\t".'<div id="techinfo">'."\n";
			echo $this->renderBacktrace()."\n";
			echo "\t\t".'</div>'."\n";
		endif;
?>
	</div>
</body>
</html>
