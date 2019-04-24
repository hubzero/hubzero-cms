<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

foreach ($list as $item) : ?>
	<li <?php if ($_SERVER['PHP_SELF'] == Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($item->id))) { echo ' class="active"'; } ?>>
		<?php $levelup = $item->level-$startLevel -1; ?>
		<h<?php echo $params->get('item_heading')+ $levelup; ?>>
			<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($item->id)); ?>">
				<?php echo $item->title; ?>
			</a>
		</h<?php echo $params->get('item_heading')+ $levelup; ?>>

		<?php
		if ($params->get('show_description', 0))
		{
			echo Html::content('prepare', $item->description, $item->getParams(), 'mod_articles_categories.content');
		}
		if ($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $startLevel))) && count($item->getChildren()))
		{
			echo '<ul>';
			$temp = $list;
			$list = $item->getChildren();
			require $this->getLayoutPath($params->get('layout', 'default') . '_items');
			$list = $temp;
			echo '</ul>';
		}
		?>
	</li>
<?php endforeach;
