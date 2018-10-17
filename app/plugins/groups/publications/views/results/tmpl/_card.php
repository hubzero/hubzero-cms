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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
// No direct access
defined('_HZEXEC_') or die();
$database = App::get('db');
// Instantiate a helper object
//$RE = new \Components\Resources\Helpers\Helper($this->row->id, $database);
//$RE->getContributors();
// Get the component params and merge with resource params
$config = Component::params('com_publications');
$rparams = new \Hubzero\Config\Registry($this->row->params);
$params = $config;
$params->merge($rparams);
if (strstr($this->row->href, 'index.php'))
{
	$this->row->href = Route::url($this->row->href);  //where href is set
	
}
switch ($this->row->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public'; break;
}
?>

<div class="<?php echo $cls; ?> resource simple-card">
	<img src="https://qubeshub.org/groups/partnerprojectsupport/File:/uploads/annie-spratt-600.jpg" alt="image placeholder">
	<h3><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></h3>
	<p><?php echo $this->escape(stripslashes($this->row->abstract)); ?></p>
</div>