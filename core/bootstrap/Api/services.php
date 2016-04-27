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
	'Bootstrap\Api\Providers\JoomlaServiceProvider',
	'Bootstrap\Api\Providers\EventServiceProvider',
	'Bootstrap\Api\Providers\TranslationServiceProvider',
	'Bootstrap\Api\Providers\DatabaseServiceProvider',
	'Bootstrap\Api\Providers\PluginServiceProvider',
	'Bootstrap\Api\Providers\ProfilerServiceProvider',
	'Bootstrap\Api\Providers\LogServiceProvider',
	'Bootstrap\Api\Providers\RouterServiceProvider',
	'Bootstrap\Api\Providers\FilesystemServiceProvider',
	// API-specific services
	'Bootstrap\Api\Providers\SessionServiceProvider',
	'Bootstrap\Api\Providers\UserServiceProvider',
	'Bootstrap\Api\Providers\ErrorServiceProvider',
	'Hubzero\Api\ResponseServiceProvider',
	'Hubzero\Api\AuthServiceProvider',
	'Hubzero\Api\ComponentServiceProvider',
	'Bootstrap\Api\Providers\MenuServiceProvider',
	'Hubzero\Api\Response\UriBase',
	'Hubzero\Api\RateLimit\RateLimitService',
	//'Hubzero\Api\Response\JsonpCallable',
	//'Hubzero\Api\Response\DateFormatter',
	//'Hubzero\Api\Response\ObjectExpander',
);