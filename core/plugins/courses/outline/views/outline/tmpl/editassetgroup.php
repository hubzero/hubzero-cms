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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$ag = new \Components\Courses\Models\Assetgroup($this->scope_id);

?>

<div class="edit-assetgroup">
	<form action="<?php echo Request::base(true); ?>/api/courses/assetgroup/save" method="POST" class="edit-form">

		<p>
			<label for="title">Title:</label>
			<input type="text" name="title" value="<?php echo $ag->get('title') ?>" placeholder="Asset Group Title" />
		</p>
		<p>
			<label for="state">Published:</label>
			<select name="state">
				<option value="0"<?php if ($ag->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JNo'); ?></option>
				<option value="1"<?php if ($ag->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JYes'); ?></option>
			</select>
		</p>

<?php
	if ($plugins = Event::trigger('courses.onAssetgroupEdit'))
	{
		$data = $ag->get('params');

		foreach ($plugins as $plugin)
		{
			$p = Plugin::byType('courses', $plugin['name']);
			$default = new \Hubzero\Config\Registry($p->params);

			$param = new \Hubzero\Html\Parameter(
				(is_object($data) ? $data->toString() : $data),
				PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
			);
			foreach ($default->toArray() as $k => $v)
			{
				if (substr($k, 0, strlen('default_')) == 'default_')
				{
					$param->def(substr($k, strlen('default_')), $default->get($k, $v));
				}
			}
			$out = $param->render('params', 'onAssetgroupEdit');
			if (!$out)
			{
				continue;
			}
			?>
			<fieldset class="eventparams" id="params-<?php echo $plugin['name']; ?>">
				<legend><?php echo Lang::txt('%s Parameters', $plugin['title']); ?></legend>
				<?php echo $out; ?>
			</fieldset>
			<?php
		}
	}
?>

		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id') ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
		<input type="hidden" name="id" value="<?php echo $ag->get('id') ?>" />

		<input type="submit" value="Submit" class="submit" />
		<input type="button" value="Cancel" class="cancel" />

	</form>
</div>