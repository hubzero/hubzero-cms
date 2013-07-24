<?php 
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=' . $this->plugin;
?>
<div class="guide-wrap">
	<div class="guide-content">
<?php
if (!$this->page)
{
	?>
	<div id="guide-introduction">
		<div class="instructions">
			<p><?php echo JText::_('No guide found.'); ?></p>
		</p>
	</div>
	<?php
}
else
{

	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => $this->course->get('alias') . DS . $this->offering->get('alias') . DS . $this->plugin,
		'pagename' => $this->page->get('url'),
		'pageid'   => '',
		'filepath' => DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $this->plugin,
		'domain'   => 'courses'
	);

	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();

	//$layout = 'page';
	$pathway =& JFactory::getApplication()->getPathway();
	$pathway->addItem(
		stripslashes($this->page->get('title')), 
		$base //. '&unit=' . $this->page->get('url')
	);
?>
<?php /*if ($this->offering->access('manage')) { ?>
		<ul class="manager-options">
			<li>
				<a class="edit" href="<?php echo JRoute::_($base . '&unit=edit'); ?>" title="<?php echo JText::_('Edit page'); ?>">
					<?php echo JText::_('Edit'); ?>
				</a>
			</li>
		</ul>
<?php }*/ ?>
		<h3><?php echo stripslashes($this->page->get('title')); ?></h3>
		<?php echo $p->parse($this->page->get('content'), $wikiconfig); ?>
<?php
}
?>
	</div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->