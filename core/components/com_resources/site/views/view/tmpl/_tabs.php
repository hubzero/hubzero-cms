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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = Request::get('tab_base_url', null) ? Request::get('tab_base_url') : 'index.php?option=' . $this->option;
$base .= '&' . ($this->resource->alias ? 'alias=' . $this->resource->alias : 'id=' . $this->resource->id);

$active_key = Request::get('tab_active_key', null) ? Request::get('tab_active_key') : 'active';

?>
<ul id="sub-menu" class="sub-menu">
	<?php
	foreach ($this->cats as $cat)
	{
		$name = key($cat);

		if (!$name)
		{
			continue;
		}

		$active = false;

		$url = $base . '&' . $active_key . '=' . $name;
		if (strtolower($name) == $this->active)
		{
			Pathway::append($cat[$name], $url);

			if ($active != 'about')
			{
				Document::setTitle(Document::getTitle() . ': ' . $cat[$name]);
			}

			$active = true;
		}
		?>
		<li id="sm-<?php echo $name; ?>"<?php echo $active ? ' class="active"' : ''; ?>>
			<a class="tab" data-rel="<?php echo $name; ?>" href="<?php echo Route::url($url); ?>"><span><?php echo $cat[$name]; ?></span></a>
		</li>
		<?php
	}
	?>
</ul>
