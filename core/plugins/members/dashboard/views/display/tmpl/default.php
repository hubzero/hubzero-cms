<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

// is the dashboard customizable?
$customizable = true;
if ($this->params->get('allow_customization', 1) == 0)
{
	$customizable = false;
}
?>

<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD'); ?>
</h3>

<?php if ($customizable) : ?>
<ul id="page_options">
	<li>
		<a class="icon-add btn add-module" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard&action=add' ); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
		</a>
	</li>
</ul>
<?php endif; ?>

<noscript>
	<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT'); ?></p>
</noscript>

<div class="modules <?php echo ($customizable) ? 'customizable' : ''; ?>" data-userid="<?php echo User::get('id'); ?>" data-token="<?php echo Session::getFormToken(); ?>">
	<?php
		foreach ($this->modules as $module)
		{
			// create view object
			$this->view('module')
			     ->set('admin', $this->admin)
			     ->set('module', $module)
			     ->display();
		}
	?>
</div>

<div class="modules-empty">
	<h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_TITLE'); ?></h3>
	<p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_DESC'); ?></p>
</div>