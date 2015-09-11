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

if ($canonical = $this->model->attribs->get('canonical', ''))
{
	$title = $canonical;
	$url   = $canonical;

	if (preg_match('/^(\/?resources\/(.+))/i', $canonical, $matches))
	{
		$model = \Components\Resources\Models\Resource::getInstance($matches[2]);
		$title = $model->resource->title;
		$url   = Route::url('index.php?option=' . $this->option . ($model->resource->alias ? '&alias=' . $model->resource->alias : '&id=' . $model->resource->id));
	}
	else if (is_numeric($canonical))
	{
		$model = \Components\Resources\Models\Resource::getInstance(intval($canonical));
		$title = $model->resource->title;
		$url   = Route::url('index.php?option=' . $this->option . ($model->resource->alias ? '&alias=' . $model->resource->alias : '&id=' . $model->resource->id));
	}

	if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $url))
	{
		$url = rtrim(Request::base(), DS) . DS . ltrim($url, DS);
	}
	?>
	<div class="new-version grid">
		<div class="col span8">
			&nbsp;
		</div>
		<div class="col span4 omega">
			<p><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL'); ?></p>
		</div>
	</div>
	<div class="new-version-message">
		<div class="inner">
			<h3><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL'); ?></h3>
			<p><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL_EXTENDED'); ?> <a href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
		</div>
	</div>
	<?php
}