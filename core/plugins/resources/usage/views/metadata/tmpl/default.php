<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<p class="usage">
	<?php if ($this->resource->type == 7) : ?>
		<a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_DETAILED', $this->stats->users); ?></a>
	<?php elseif ($this->stats->users) : ?>
		<?php echo Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS', $this->stats->users); ?>
	<?php endif; ?>
</p>

<?php if ($this->clusters->users && $this->clusters->classes) : ?>
	<p class="usage">
		<?php echo Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_IN_CLASSES', $clusters->users, $clusters->classes); ?>
	</p>
<?php endif;
