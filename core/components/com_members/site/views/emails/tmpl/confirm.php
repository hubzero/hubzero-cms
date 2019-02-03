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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->baseURL = rtrim($this->baseURL, '/');

$link = Config::get('sef') && App::isAdmin()
	? '/members/confirm?confirm=' . -$this->confirm
	: Route::url('index.php?option=' . $this->option . '&task=confirm&confirm=' . -$this->confirm . '&email=' . urlencode($this->email), false);
$link = $this->baseURL . $link;
$link = str_replace('/administrator', '', $this->baseURL);
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED'); ?>: <?php echo $this->registerDate; ?> (UTC)
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME'); ?>: <?php echo $this->name; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME'); ?>: <?php echo $this->login; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_MESSAGE', $this->sitename); ?>

<?php echo $link;
