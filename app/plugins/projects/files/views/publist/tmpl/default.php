<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

require_once( PATH_CORE . DS . 'components' . DS
	.'com_projects' . DS . 'tables' . DS . 'publicstamp.php');

$database 	= App::get('db');
$objSt 		= new \Components\Projects\Tables\Stamp( $database );

// Get listed public files
$items = $objSt->getPubList($this->model->get('id'), 'files');

if ($items) {
?>
<div class="public-list-header">
	<h3><?php echo ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')); ?> <?php echo Lang::txt('COM_PROJECTS_FILES'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($items as $item)
		{
			$ref = json_decode($item->reference);
			$file = new \Components\Projects\Models\File($e);
		?>
		<li><a href="<?php echo Route::url($this->model->link('stamp') . '&s=' . $item->stamp); ?>"><?php echo $file::drawIcon($file->get('ext')); ?> <?php echo basename($ref->file); ?></li>
		<?php
		} ?>
	</ul>
</div>
<?php } ?>
