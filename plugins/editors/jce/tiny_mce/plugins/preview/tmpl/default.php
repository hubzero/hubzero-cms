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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
	<?php
	$db = & JFactory::getDbo();
		$query	= 'SELECT template'
				. ' FROM #__templates_menu'
				. ' WHERE client_id = 0 AND menuid = 0';

		$db->setQuery($query);
		$template = $db->loadResult();

 if($this->direction == 'rtl' && !file_exists(JPATH_THEMES.DS.$template.DS.'css/template_rtl.css') || !file_exists(JPATH_THEMES.DS.$template.DS.'css/template.css')) : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/template_rtl.css" type="text/css" />
<?php elseif($this->direction == 'rtl' ) : ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $template; ?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $template; ?>/css/template_rtl.css" type="text/css" />
<?php elseif($this->direction == 'ltr' && !file_exists(JPATH_THEMES.DS.$template.DS.'css/template.css')) : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/template.css" type="text/css" />
<?php elseif($this->direction == 'ltr' ) : ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $template; ?>/css/template.css" type="text/css" />
<?php endif; ?>
</head>
<body class="contentpane">
	<?php  if (!$this->params->get('show_intro')) :
	echo $this->article->event->afterDisplayTitle;
	endif; ?>
	<?php echo $this->article->event->beforeDisplayContent; ?>
	<table class="contentpaneopen<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
	<tr>
		<td>
			<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
			<span>
				<?php if ($this->params->get('link_section')) : ?>
					<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
				<?php endif; ?>
				<?php echo $this->escape($this->article->section); ?>
				<?php if ($this->params->get('link_section')) : ?>
					<?php echo '</a>'; ?>
				<?php endif; ?>
					<?php if ($this->params->get('show_category')) : ?>
					<?php echo ' - '; ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>
			<?php if ($this->params->get('show_category') && $this->article->catid) : ?>
			<span>
				<?php if ($this->params->get('link_category')) : ?>
					<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
				<?php endif; ?>
				<?php echo $this->escape($this->article->category); ?>
				<?php if ($this->params->get('link_category')) : ?>
					<?php echo '</a>'; ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>
		</td>
	</tr>
	<?php endif; ?>
	
	<tr>
	<td valign="top">
	<?php if (isset ($this->article->toc)) : ?>
		<?php echo $this->article->toc; ?>
	<?php endif; ?>
	<?php echo $this->article->text; ?>
	</td>
	</tr>
	
	</table>
	<span class="article_separator">&nbsp;</span>
	<?php echo $this->article->event->afterDisplayContent; ?>
</body>
</html>