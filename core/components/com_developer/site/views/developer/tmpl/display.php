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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// include css
$this->css('introduction', 'system')
     ->css('intro')
     ->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER'); ?></h2>
</header>

<section class="main section developer-section api">
	<div class="section-inner">
		<div class="grid">
			<div class="col span8">
				<h3><?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_developer&controller=api'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
			<div class="col span4 omega">
				<div class="icon-api"></div>
			</div>
		</div>
	</div>
</section>

<?php /*
<section class="main section developer-section web">
	<div class="section-inner">
		<div class="grid">
			<div class="col span4">
				<div class="icon-web"></div>
			</div>
			<div class="col span8 omega">
				<h3><?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_developer&controller=web'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
		</div>
	</div>
</section>
*/ ?>

<section class="main section developer-section tool">
	<div class="section-inner">
		<div class="grid">
			<div class="col span4">
				<div class="icon-tool"></div>
			</div>
			<div class="col span8 omega">
				<h3><?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_tools'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
		</div>
	</div>
</section>