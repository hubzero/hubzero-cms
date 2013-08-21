<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$app =& JFactory::getApplication();
$pathway =& $app->getPathway();

// J2.5 compatibility
if (version_compare(JVERSION, '2.5', 'ge'))
{
	JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

	$this->article = $this->item;
	$params        = $this->item->params;
	$images        = json_decode($this->item->images);
	$urls          = json_decode($this->item->urls);
	$canEdit       = $this->item->params->get('access-edit');
	$user          = JFactory::getUser();
}
else
{
	$canEdit = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
	$params = $this->params;
}

if (count($pathway->getPathWay()) <= 0) {
	//$pathway->addItem($this->escape($this->article->title),$this->article->readmore_link);
	$pathway->addItem($this->escape($this->article->title),$_SERVER['REQUEST_URI']);
}
?>
<?php if ($canEdit || $params->get('show_title') || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
<div class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?> heading">
	<?php if ($params->get('show_title')) : ?>
	<div class="content-header">
	<h2 class="contentheading<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<?php if ($params->get('link_titles') && $this->article->readmore_link != '') : ?>
		<a href="<?php echo $this->article->readmore_link; ?>" class="contentpagetitle<?php echo $params->get( 'pageclass_sfx' ); ?>"><?php echo $this->escape($this->article->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->article->title); ?>
		<?php endif; ?>
	</h2>
	</div>
	<?php endif; ?>
	<?php if (!$this->print) : ?>
<?php if ($params->get( 'show_print_icon' ) || $params->get('show_email_icon')) : ?>
		<p class="buttonheadings">
		<?php if ( $params->get( 'show_print_icon' )) : ?>
		<span class="buttonheading">
		<?php echo JHTML::_('icon.print_popup',  $this->article, $params, $this->access); ?>
		</span>
		<?php endif; ?>

		<?php if ($params->get('show_email_icon')) : ?>
		<span class="buttonheading">
		<?php echo JHTML::_('icon.email',  $this->article, $params, $this->access); ?>
		</span>
		<?php endif; ?>
		<?php /*if ($canEdit) : ?>
		<span class="buttonheading">
			<?php echo JHTML::_('icon.edit', $this->article, $params, $this->access); ?>
		</span>
		<?php endif;*/ ?>
		</p>
<?php endif; ?>
	<?php else : ?>
		<p class="buttonheadings">
		<?php echo JHTML::_('icon.print_screen',  $this->article, $params, $this->access); ?>
		</p>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php  if (!$params->get('show_intro')) :
	echo $this->article->event->afterDisplayTitle;
endif; ?>
<?php echo $this->article->event->beforeDisplayContent; ?>
<div class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
<?php if (($params->get('show_section') && $this->article->sectionid) || ($params->get('show_category') && $this->article->catid)) : ?>
	<p>
		<?php if ($params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
		<span>
			<?php if ($params->get('link_section')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->article->section; ?>
			<?php if ($params->get('link_section')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
				<?php if ($params->get('show_category')) : ?>
				<?php echo ' - '; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
		<?php if ($params->get('show_category') && $this->article->catid) : ?>
		<span>
			<?php if ($params->get('link_category')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->article->category; ?>
			<?php if ($params->get('link_category')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
	</p>
<?php endif; ?>
<?php if (($params->get('show_author')) && ($this->article->author != "")) : ?>
	<p class="aticleauthor">
		<span class="small">
			<?php JText::printf( 'Written by', ($this->article->created_by_alias ? $this->article->created_by_alias : $this->article->author) ); ?>
		</span>
	</p>
<?php endif; ?>

<?php if ($params->get('show_create_date')) : ?>
	<p class="createdate">
		<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?>
	</p>
<?php endif; ?>

<?php if ($params->get('show_url') && $this->article->urls) : ?>
	<p>
		<a href="http://<?php echo $this->article->urls ; ?>" rel="external"><?php echo $this->article->urls; ?></a>
	</p>
<?php endif; ?>

<?php if (isset ($this->article->toc)) : ?>
	<?php echo $this->article->toc; ?>
<?php endif; ?>
<?php echo $this->article->text; ?>

<?php if ( intval($this->article->modified) !=0 && $params->get('show_modify_date')) : ?>
	<p class="modifydate">
		<?php echo JText::_( 'Last Updated' ); ?> ( <?php echo JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2')); ?> )
	</p>
<?php endif; ?>
</div>
<p class="article_separator">&nbsp;</p>
<?php echo $this->article->event->afterDisplayContent; ?>
