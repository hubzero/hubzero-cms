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

$prov = $this->pub->_project->isProvisioned() ? 1 : 0;
$action = 'select';

switch ($this->type)
{
	case 'file':
	default:
		$active = 'files';
		break;
	case 'data':
		$active = 'databases';
		break;
	case 'link':
		$active = 'links';
		break;
	case 'publication':
		$active = 'publications';
		//$action = 'choose';
		break;
}

$route = $this->pub->link('editbase');
$selectUrl = $prov
		? Route::url($route) . '?active=' . $active . '&amp;action=' . $action . '&amp;p=' . $this->props . '&amp;pid=' . $this->pub->id . '&amp;vid=' . $this->pub->version_id
		: Route::url($route . '&active=' . $active . '&action=' . $action . '&p=' . $this->props . '&pid=' . $this->pub->id . '&vid=' . $this->pub->version_id);

?>
<div class="item-new">
	<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox nox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECT_' . strtoupper($this->type)); ?></a></span>
</div>