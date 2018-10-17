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

$database = \App::get('db');
switch ($this->filters['sortby'])
{
	case 'date_created': $show_date = 1; break;
	case 'date_modified': $show_date = 2; break;
	case 'date':
	default: $show_date = 3; break;
}

// Get version authors
$pa = new \Components\Publications\Tables\Author( $database );
?>

<ol class="results" id="publications">
<?php

foreach ($this->results as $line)
{

	$authors = $pa->getAuthors($line->version_id);

	// Get parameters
	$params = clone($this->config);
	$rparams = new \Hubzero\Config\Registry( $line->params );
	$params->merge( $rparams );

	// Set the display date
	switch ($show_date)
	{
		case 0: $thedate = ''; break;
		case 1: $thedate = $line->created();      break;
		case 2: $thedate = $line->modified();     break;
		case 3: $thedate = $line->published();    break;
	}

	// Display List of items
	$this->view('item')
	     ->set('option', 'com_publications')
	     ->set('filters', $this->filters)
	     ->set('config', $this->config)
	     ->set('authors', $authors)
	     ->set('line', $line)
	     ->set('thedate', $thedate)
	     ->set('params', $params)
	     ->display();
}
?>
</ol>