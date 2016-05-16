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
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

return array(
	// Base Services
	'Bootstrap\Cli\Providers\JoomlaServiceProvider',
	'Bootstrap\Cli\Providers\EventServiceProvider',
	'Bootstrap\Cli\Providers\TranslationServiceProvider',
	'Bootstrap\Cli\Providers\DatabaseServiceProvider',
	'Bootstrap\Cli\Providers\PluginServiceProvider',
	'Bootstrap\Cli\Providers\ProfilerServiceProvider',
	'Bootstrap\Cli\Providers\LogServiceProvider',
	'Bootstrap\Cli\Providers\RouterServiceProvider',
	'Bootstrap\Cli\Providers\FilesystemServiceProvider',
	// CLI-specific services
	'Bootstrap\Cli\Providers\SessionServiceProvider',
	'Bootstrap\Cli\Providers\UserServiceProvider',
	'Hubzero\Console\ArgumentsServiceProvider',
	'Hubzero\Console\OutputServiceProvider',
	'Hubzero\Console\DispatcherServiceProvider',
	'Hubzero\Console\ComponentServiceProvider',
);