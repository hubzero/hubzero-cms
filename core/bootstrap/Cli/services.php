<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

return array(
	// Base Services
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
