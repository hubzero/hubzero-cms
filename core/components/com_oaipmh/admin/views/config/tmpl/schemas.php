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

defined('_HZEXEC_') or die();

$canDo = \Components\Oaipmh\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_OAIPMH_SETTINGS'), 'generic.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_oaipmh', 500);
	Toolbar::spacer();
}
Toolbar::help('oaipmh');

$this->css();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_PREFIX'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_FORMAT'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->service->getSchemas() as $name) { ?>
			<tr>
				<?php
				$this->service->setSchema($name);
				$schema = $this->service->getSchema();
				?>
				<th><?php echo $schema->name(); ?></th>
				<td><?php echo $schema->prefix(); ?></td>
				<td><code>&amp;metadataPrefix=<?php echo $schema->prefix(); ?></code></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>