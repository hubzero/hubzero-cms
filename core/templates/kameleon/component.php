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

// Load base styles
$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/component.css?v=' . filemtime(__DIR__ . DS . 'css' . DS . 'component.css'));
// Load theme
if ($theme = $this->params->get('theme'))
{
	if ($theme == 'custom')
	{
		$color = $this->params->get('color');
		$this->addStyleDeclaration(include_once(__DIR__ . DS . 'css' . DS . 'themes' . DS . 'custom.php'));
	}
	else if ($theme != 'gray')
	{
		$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/themes/' . $theme . '.css');
	}
}
// Load language direction CSS
if ($this->direction == 'rtl')
{
	$this->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/common/rtl.css');
}

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/component.js');

$browser = new \Hubzero\Browser\Detector();
?>
<!DOCTYPE html>
<html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="<?php echo $browser->name() . ' ' . $browser->name() . $browser->major(); ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body id="component-body" class="contentpane">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>