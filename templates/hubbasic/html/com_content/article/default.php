<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));

$app = JFactory::getApplication();
$pathway = $app->getPathway();
if (count($pathway->getPathWay()) <= 0) {
	//$pathway->addItem($this->escape($this->article->title),$this->article->readmore_link);
	$pathway->addItem($this->escape($this->article->title),$_SERVER['REQUEST_URI']);
}
?>
<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
<div class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?> heading">
	<?php if ($this->params->get('show_title')) : ?>
	<div class="content-header">
	<h2 class="contentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
		<a href="<?php echo $this->article->readmore_link; ?>" class="contentpagetitle<?php echo $this->params->get( 'pageclass_sfx' ); ?>"><?php echo $this->escape($this->article->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->article->title); ?>
		<?php endif; ?>
	</h2>
	</div>
	<?php endif; ?>
	<?php if (!$this->print) : ?>
		<p class="buttonheadings">
		<?php if ( $this->params->get( 'show_print_icon' )) : ?>
		<span class="buttonheading">
		<?php echo JHTML::_('icon.print_popup',  $this->article, $this->params, $this->access); ?>
		</span>
		<?php endif; ?>

		<?php if ($this->params->get('show_email_icon')) : ?>
		<span class="buttonheading">
		<?php echo JHTML::_('icon.email',  $this->article, $this->params, $this->access); ?>
		</span>
		<?php endif; ?>
		<?php if ($canEdit) : ?>
		<span class="buttonheading">
			<?php echo JHTML::_('icon.edit', $this->article, $this->params, $this->access); ?>
		</span>
		<?php endif; ?>
		</p>
	<?php else : ?>
		<p class="buttonheadings">
		<?php echo JHTML::_('icon.print_screen',  $this->article, $this->params, $this->access); ?>
		</p>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php  if (!$this->params->get('show_intro')) :
	echo $this->article->event->afterDisplayTitle;
endif; ?>
<?php echo $this->article->event->beforeDisplayContent; ?>
<div class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
	<p>
		<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
		<span>
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->article->section; ?>
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
			<?php echo $this->article->category; ?>
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
		</span>
		<?php endif; ?>
	</p>
<?php endif; ?>
<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
	<p class="aticleauthor">
		<span class="small">
			<?php JText::printf( 'Written by', ($this->article->created_by_alias ? $this->article->created_by_alias : $this->article->author) ); ?>
		</span>
	</p>
<?php endif; ?>

<?php if ($this->params->get('show_create_date')) : ?>
	<p class="createdate">
		<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?>
	</p>
<?php endif; ?>

<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
	<p>
		<a href="http://<?php echo $this->article->urls ; ?>" rel="external"><?php echo $this->article->urls; ?></a>
	</p>
<?php endif; ?>

<?php if (isset ($this->article->toc)) : ?>
	<?php echo $this->article->toc; ?>
<?php endif; ?>
<?php echo $this->article->text; ?>

<?php if ( intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
	<p class="modifydate">
		<?php echo JText::_( 'Last Updated' ); ?> ( <?php echo JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2')); ?> )
	</p>
<?php endif; ?>
</div>
<p class="article_separator">&nbsp;</p>
<?php echo $this->article->event->afterDisplayContent; ?>
