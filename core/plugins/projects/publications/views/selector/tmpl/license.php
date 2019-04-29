<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pubParams = $this->publication->params;

?>
<ul class="pub-selector" id="pub-selector">
	<?php foreach ($this->selections as $item)
	{
		// Select license if
		//   (1) license type matches item id, or
		//   (2) there is no license type yet and this item is the default license
		$selected = ($this->publication->get('license_type') && $this->publication->get('license_type') == $item->id) || (!$this->publication->get('license_type') && $item->main) ? true : false;

		$liId = 'choice-' . $item->id;

		$info = $item->info;
		if ($item->url)
		{
			$info .= ' <a href="' . $item->url . '" rel="nofollow external">' . Lang::txt('Read license terms &rsaquo;') . '</a>';
		}

		$icon = $item->icon;
		$icon = str_replace('/components/com_publications/assets/img/', '/core/components/com_publications/site/assets/img/', $icon);

		?>
		<li class="type-license allowed <?php if ($selected) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
			<span class="item-info"></span>
			<span class="item-wrap">
			<?php if ($item->icon) { echo '<img src="' . $icon . '" alt="' . htmlentities($item->title) . '" />'; } ?><?php echo $item->title; ?>
			</span>
			<span class="item-fullinfo">
				<?php echo $info; ?>
			</span>
		</li>
	<?php } ?>
</ul>

<?php if ($this->publication->config()->get('suggest_licence')) { ?>
	<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_DONT_SEE_YOURS') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_YOU_CAN'); ?> <a href="<?php echo $this->url . '?action=suggest_license&amp;version=' . $this->publication->get('version_number'); ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGEST'); ?></a></p>
<?php }
