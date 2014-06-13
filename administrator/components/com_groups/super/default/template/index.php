<?php
/**
 * Basic Template
 *
 * Template used for Special Groups. Will now be auto-created 
 * when admin switches group from type HUB to type Special.
 *
 * @author 		Christopher Smoak
 * @copyright	December 2012
 */

// define base path (without doc root)
$base = rtrim(str_replace(JPATH_ROOT, '', __DIR__), DS);

// define base url for links
$baseLink = 'index.php?option=com_groups&cn=' . $this->group->get('cn');

// check to see if were supposed to no display html (template frame)
$no_html = JRequest::getInt('no_html', 0);

// add stylesheets and scripts
JFactory::getDocument()
	->addStyleSheet( $base . DS . 'assets/css/main.css' )
	->addScript( $base . DS . 'assets/js/main.js' );
?>

<?php if (!$no_html) : ?>
<group:include type="content" scope="before" />

<div class="super-group-body-wrap group-<?php echo $this->group->get('cn'); ?>">
	<div class="super-group-body">
		<?php include_once 'includes/header.php'; ?>
		
		<div class="super-group-content-wrap">
			<div class="super-group-content group_<?php echo $this->tab; ?>">
				<?php 
					$show = array('members', 'resources', 'wiki', 'forum', 'blog', 'calendar', 'usage', 'wishlist', 'announcements');
					if (in_array($this->tab, $show)) : ?>
					<h2><?php echo ($this->tab == 'forum') ? 'Discussions' : ucfirst($this->tab); ?></h2>
				<?php endif; ?>
<?php endif; ?>
				<!-- ###  Start Content Include  ### -->
					<group:include type="content" />
				<!-- ###  End Content Include  ### -->
<?php if (!$no_html) : ?>
			</div>
		</div>
		
		<?php include_once 'includes/footer.php'; ?>
	</div>
</div>

<group:include type="googleanalytics" account="" />
<?php endif; ?>