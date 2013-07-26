<?php 
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=' . $this->plugin;

$view = new Hubzero_Plugin_View(
	array(
		'folder'  => 'courses',
		'element' => $this->plugin,
		'name'    => $this->plugin,
		'layout'  => 'default_menu'
	)
);
$view->option     = $this->option;
$view->controller = $this->controller;
$view->course     = $this->course;
$view->offering   = $this->offering;
$view->page       = $this->page;
$view->plugin     = $this->plugin;
$view->display();
?>

<?php if (!$this->page) { ?>
	<div id="guide-introduction">
		<div class="instructions">
			<p><?php echo JText::_('No guide pages found.'); ?></p>
		</p>
	</div>
<?php } else {
	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => $this->course->get('alias') . DS . $this->offering->get('alias') . DS . $this->plugin,
		'pagename' => $this->page->get('url'),
		'pageid'   => '',
		'filepath' => DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . 'pagefiles',
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
	?>
	<div class="guide-wrap">
		<div class="guide-content">
			<?php echo $p->parse($this->page->get('content'), $wikiconfig); ?>
		</div><!-- / .guide-content -->
	</div><!-- / .guide-wrap -->
<?php } ?>