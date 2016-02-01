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
 * @author    Brandon Beatty
 * @author		Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_GEOSEARCH'));
Toolbar::preferences($this->option, 500);


$this->js('https://maps.googleapis.com/maps/api/js?v=3.exp');

$this->js();
$this->css();

?>

<div class="map-editor">
	<!-- Menu bar -->
	<div class="menu-bar">
		<span id="exit-button">Close</span>
	</div>

	<div class="editor-container">
		<span class="item-title">Name: </span>
		<span class="location-title">Location: </span>
		<!-- map container -->
		<div id="map_container">
			<div id="map_canvas"></div>
		</div> <!-- / #map_container -->

	</div>
</div>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="item-form">

<table class="adminform">
<thead>
	<th>
		<input type="checkbox" name="selectall" />
	</th>
	<th>
		<?php echo Lang::txt('ID'); ?>
	</th>
	<th>
		<?php echo Lang::txt('SCOPE'); ?>
	</th>
	<th>
		<?php echo Lang::txt('SCOPE_ID'); ?>
	</th>
	<th>
		<?php echo Lang::txt('LONGITUDE'); ?>
	</th>
	<th>
		<?php echo Lang::txt('LATITIUDE'); ?>
	</th>
	<th></th>
</thead>
<tbody>
<?php foreach ($this->markers as $marker): ?>
<tr data-scope="<?php echo $marker->scope; ?>" data-scopeID="<?php echo $marker->scope_id; ?>">
	<td>
		<input type="checkbox" name="selected[]" value="<?php echo $marker->id; ?>" />
	</td>

	<td>
		<?php echo $marker->id; ?>
	</td>

	<td>
		<?php echo $marker->scope; ?>
	</td>

	<td>
		<?php echo $marker->scope_id; ?>
	</td>

	<td>
		<?php echo $marker->addressLongitude; ?>
	</td>

	<td>
		<?php echo $marker->addressLatitude; ?>
	</td>

	<td>
		<button class="adjust" value="<?php echo $marker->id; ?>"><?php echo Lang::txt('ADJUST_POSITION'); ?></button>
		<button class="remove danger" value="<?php echo $marker->id; ?>"><?php echo Lang::txt('REMOVE_MARKER'); ?></button>
	</td>
</tr>
<?php endforeach; ?>	
</tbody>
</table>
</form>
