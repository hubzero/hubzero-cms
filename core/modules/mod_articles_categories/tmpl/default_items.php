<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

foreach ($list as $item) : ?>
	<li <?php if ($_SERVER['PHP_SELF'] == Route::url(ContentHelperRoute::getCategoryRoute($item->id))) echo ' class="active"';?>>
		<?php $levelup = $item->level-$startLevel -1; ?>
		<h<?php echo $params->get('item_heading')+ $levelup; ?>>
			<a href="<?php echo Route::url(ContentHelperRoute::getCategoryRoute($item->id)); ?>">
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
<?php endforeach; ?>
