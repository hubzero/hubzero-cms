<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

return array(
	// Base Services
	'Bootstrap\Api\Providers\JoomlaServiceProvider',
	'Bootstrap\Api\Providers\EventServiceProvider',
	'Bootstrap\Api\Providers\TranslationServiceProvider',
	'Bootstrap\Api\Providers\DatabaseServiceProvider',
	'Bootstrap\Api\Providers\PluginServiceProvider',
	//'Bootstrap\Api\Providers\ProfilerServiceProvider',
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
	'Hubzero\Api\Response\UriBase',
	'Hubzero\Api\RateLimit\RateLimitService',
	'Hubzero\Api\Response\JsonpCallable',
	//'Hubzero\Api\Response\DateFormatter',
	//'Hubzero\Api\Response\ObjectExpander',
);
