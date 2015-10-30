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
	'Hubzero\Base\JoomlaServiceProvider',
	'Hubzero\Events\EventServiceProvider',
	'Hubzero\Language\TranslationServiceProvider',
	'Hubzero\Database\DatabaseServiceProvider',
	'Hubzero\Plugin\PluginServiceProvider',
	'Hubzero\Debug\ProfilerServiceProvider',
	'Hubzero\Log\LogServiceProvider',
	'Hubzero\Routing\RouterServiceProvider',
	'Hubzero\Filesystem\FilesystemServiceProvider',
	// Site-specific services
	'Hubzero\Component\ComponentServiceProvider',
	'Hubzero\Error\ErrorServiceProvider',
	'Hubzero\Session\SessionServiceProvider',
	'Hubzero\Auth\AuthServiceProvider',
	'Hubzero\Document\DocumentServiceProvider',
	'Hubzero\Module\ModuleServiceProvider',
	'Hubzero\Pathway\PathwayServiceProvider',
	'Hubzero\Notification\NotificationServiceProvider',
	'Hubzero\Template\TemplateServiceProvider',
	'Hubzero\Cache\CacheServiceProvider',
	'Hubzero\Html\EditorServiceProvider',
	'Hubzero\Html\BuilderServiceProvider',
	'Hubzero\Mail\MailerServiceProvider',
	'Hubzero\Menu\MenuServiceProvider',
	'Hubzero\Content\FeedServiceProvider',
);