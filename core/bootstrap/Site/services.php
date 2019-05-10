<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

return array(
	// Base Services
	'Bootstrap\Site\Providers\EventServiceProvider',
	'Bootstrap\Site\Providers\TranslationServiceProvider',
	'Bootstrap\Site\Providers\DatabaseServiceProvider',
	'Bootstrap\Site\Providers\PluginServiceProvider',
	'Bootstrap\Site\Providers\ProfilerServiceProvider',
	'Bootstrap\Site\Providers\LogServiceProvider',
	'Bootstrap\Site\Providers\RouterServiceProvider',
	'Bootstrap\Site\Providers\FilesystemServiceProvider',
	// Site-specific services
	'Bootstrap\Site\Providers\ComponentServiceProvider',
	'Bootstrap\Site\Providers\ErrorServiceProvider',
	'Bootstrap\Site\Providers\SessionServiceProvider',
	'Bootstrap\Site\Providers\AuthServiceProvider',
	'Bootstrap\Site\Providers\UserServiceProvider',
	'Bootstrap\Site\Providers\DocumentServiceProvider',
	'Bootstrap\Site\Providers\ModuleServiceProvider',
	'Bootstrap\Site\Providers\PathwayServiceProvider',
	'Bootstrap\Site\Providers\NotificationServiceProvider',
	'Bootstrap\Site\Providers\TemplateServiceProvider',
	'Bootstrap\Site\Providers\CacheServiceProvider',
	'Bootstrap\Site\Providers\EditorServiceProvider',
	'Bootstrap\Site\Providers\BuilderServiceProvider',
	'Bootstrap\Site\Providers\MailerServiceProvider',
	'Bootstrap\Site\Providers\MenuServiceProvider',
	'Bootstrap\Site\Providers\FeedServiceProvider',
);
