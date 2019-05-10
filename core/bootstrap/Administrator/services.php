<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

return array(
	// Base Services
	'Bootstrap\Administrator\Providers\EventServiceProvider',
	'Bootstrap\Administrator\Providers\TranslationServiceProvider',
	'Bootstrap\Administrator\Providers\DatabaseServiceProvider',
	'Bootstrap\Administrator\Providers\PluginServiceProvider',
	'Bootstrap\Administrator\Providers\ProfilerServiceProvider',
	'Bootstrap\Administrator\Providers\LogServiceProvider',
	'Bootstrap\Administrator\Providers\RouterServiceProvider',
	'Bootstrap\Administrator\Providers\FilesystemServiceProvider',
	// Admin-specific services
	'Bootstrap\Administrator\Providers\ComponentServiceProvider',
	'Bootstrap\Administrator\Providers\ErrorServiceProvider',
	'Bootstrap\Administrator\Providers\SessionServiceProvider',
	'Bootstrap\Administrator\Providers\AuthServiceProvider',
	'Bootstrap\Administrator\Providers\UserServiceProvider',
	'Bootstrap\Administrator\Providers\DocumentServiceProvider',
	'Bootstrap\Administrator\Providers\ToolbarServiceProvider',
	'Bootstrap\Administrator\Providers\ModuleServiceProvider',
	'Bootstrap\Administrator\Providers\NotificationServiceProvider',
	'Bootstrap\Administrator\Providers\TemplateServiceProvider',
	'Bootstrap\Administrator\Providers\CacheServiceProvider',
	'Bootstrap\Administrator\Providers\EditorServiceProvider',
	'Bootstrap\Administrator\Providers\BuilderServiceProvider',
	'Bootstrap\Administrator\Providers\MailerServiceProvider',
	'Bootstrap\Administrator\Providers\MenuServiceProvider',
	'Bootstrap\Administrator\Providers\FeedServiceProvider',
);
