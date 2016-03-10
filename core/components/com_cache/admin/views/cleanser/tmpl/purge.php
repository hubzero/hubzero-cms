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

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CACHE_PURGE_EXPIRED_CACHE'), 'purge.png');
Toolbar::custom('purge', 'delete.png', 'delete_f2.png', 'COM_CACHE_PURGE_EXPIRED', false);
Toolbar::divider();
if (User::authorise('core.admin', 'com_cache'))
{
	Toolbar::preferences('com_cache');
	Toolbar::divider();
}
Toolbar::help('purge_expired');
?>

<form action="<?php echo Route::url('index.php?option=com_cache'); ?>" method="post" name="adminForm" id="item-form">

	<p class="mod-purge-instruct"><?php echo Lang::txt('COM_CACHE_PURGE_INSTRUCTIONS'); ?></p>
	<p class="warning"><?php echo Lang::txt('COM_CACHE_RESOURCE_INTENSIVE_WARNING'); ?></p>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_cache" />

	<?php echo Html::input('token'); ?>
</form>
