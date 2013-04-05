<?php 
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=pages';

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => '',
	'pagename' => DS . $this->page->get('url'),
	'pageid'   => $this->course->get('id'),
	'filepath' => DS . trim($this->config->get('uploadpath', '/site/courses'), DS),
	'domain'   => $this->course->get('alias')
);

ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

//$layout = 'page';
$pathway =& JFactory::getApplication()->getPathway();
$pathway->addItem(
	stripslashes($this->page->get('title')), 
	$base . '&unit=' . $this->page->get('url')
);

ximport('Hubzero_Wiki_Parser');
$view = new Hubzero_Plugin_View(
	array(
		'folder'  => 'courses',
		'element' => 'pages',
		'name'    => 'pages',
		'layout'  => 'default_menu'
	)
);
$view->option     = $this->option;
$view->controller = $this->controller;
$view->course     = $this->course;
$view->offering   = $this->offering;
$view->page       = $this->page;
$view->display();
?>
<div class="pages-wrap">
	<div class="pages-content">
<?php if ($this->offering->access('manage')) { ?>
		<ul class="manager-options">
			<li>
				<a class="delete" href="<?php echo JRoute::_($base . '&unit=' . $this->page->get('url') . '&b=delete'); ?>" title="<?php echo JText::_('Delete page'); ?>">
					<?php echo JText::_('Delete'); ?>
				</a>
			</li>
			<li>
				<a class="edit" href="<?php echo JRoute::_($base . '&unit=' . $this->page->get('url') . '&b=edit'); ?>" title="<?php echo JText::_('Edit page'); ?>">
					<?php echo JText::_('Edit'); ?>
				</a>
			</li>
		</ul>
<?php } ?>
<?php echo $p->parse($this->page->get('content'), $wikiconfig); ?>
	</div><!-- / .pages-content -->
</div><!-- / .pages-wrap -->