<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div<?php echo ($this->params->get('cssId')) ? ' id="' . $this->params->get('cssId') . '"' : ''; ?>>
	<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="search">
		<fieldset>
			<p>
				<label for="rsearchword"><?php echo Lang::txt('MOD_FINDRESOURCES_SEARCH_LABEL'); ?></label>
				<input type="text" name="terms" id="rsearchword" value="" />
				<input type="hidden" name="domain" value="resources" />
				<input type="submit" value="<?php echo Lang::txt('MOD_FINDRESOURCES_SEARCH'); ?>" />
			</p>
		</fieldset>
	</form>
<?php if (count($this->tags) > 0) { ?>
	<ol class="tags">
		<li><?php echo Lang::txt('MOD_FINDRESOURCES_POPULAR_TAGS'); ?></li>
		<?php foreach ($this->tags as $tag) { ?>
			<li><a href="<?php echo Route::url('index.php?option=com_tags&tag=' . $tag->tag); ?>"><?php echo $this->escape(stripslashes($tag->raw_tag)); ?></a></li>
		<?php } ?>
		<li><a href="<?php echo Route::url('index.php?option=com_tags'); ?>" class="showmore"><?php echo Lang::txt('MOD_FINDRESOURCES_MORE_TAGS'); ?></a></li>
	</ol>
<?php } else { ?>
	<p><?php echo Lang::txt('MOD_FINDRESOURCES_NO_TAGS'); ?></p>
<?php } ?>

<?php if (count($this->categories) > 0) { ?>
	<p>
		<?php
		$i = 0;
		foreach ($this->categories as $category)
		{
			$i++;
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($category->type));

			if (substr($normalized, -3) == 'ies') {
				$cls = $normalized;
			} else {
				$cls = substr($normalized, 0, -1);
			}
			?>
			<a href="<?php echo Route::url('index.php?option=com_resources&type=' . $normalized); ?>"><?php echo $this->escape(stripslashes($category->type)); ?></a><?php echo ($i == count($this->categories)) ? '...' : ', '; ?>
			<?php
		}
		?>
		<a href="<?php echo Route::url('index.php?option=com_resources'); ?>" class="showmore"><?php echo Lang::txt('MOD_FINDRESOURCES_ALL_CATEGORIES'); ?></a>
	</p>
<?php } ?>
	<div class="uploadcontent">
		<h4><?php echo Lang::txt('MOD_FINDRESOURCES_UPLOAD_CONTENT'); ?> <span><a href="<?php echo Route::url('index.php?option=com_resources&task=new'); ?>" class="contributelink"><?php echo Lang::txt('MOD_FINDRESOURCES_GET_STARTED'); ?></a></span></h4>
	</div>
</div><!-- / <?php echo ($this->params->get('cssId')) ? '#' . $this->params->get('cssId') : ''; ?> -->
