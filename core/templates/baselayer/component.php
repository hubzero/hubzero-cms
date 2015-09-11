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
 * @author    Ilya Shunko
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->template = 'baselayer';

$browser = new \Hubzero\Browser\Detector();

$cls = array(
	$browser->name(),
	$browser->name() . $browser->major()
);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo  $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>" class="<?php echo implode(' ', $cls); ?>"> <!--<![endif]-->
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" />

		<?php if ($this->direction == 'rtl' && (!file_exists(__DIR__ . DS . 'css/component_rtl.css') || !file_exists(__DIR__ . DS . 'css/component.css'))) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template_rtl.css" type="text/css" />
		<?php elseif ($this->direction == 'rtl' ) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component.css" type="text/css" />
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component_rtl.css" type="text/css" />
		<?php elseif ($this->direction == 'ltr' && !file_exists(__DIR__ . DS . 'css/component.css')) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/template.css" type="text/css" />
		<?php elseif ($this->direction == 'ltr' ) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/component.css" type="text/css" />
		<?php endif; ?>

		<jdoc:include type="head" />

		<!--[if IE 8]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie8win.css" />
		<![endif]-->
		<!--[if IE 7]>
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl; ?>/templates/<?php echo $template; ?>/css/ie7win.css" />
		<![endif]-->
	</head>
	<body class="contentpane" id="component-body">
		<jdoc:include type="message" />
		<div id="content">
			<jdoc:include type="component" />
		</div>
	</body>
</html>