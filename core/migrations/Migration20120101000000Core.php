<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for determining if 2011/12 migrations should be run
 **/
class Migration20120101000000Core extends Base
{
	public function up()
	{
		$files = array(
			'Migration20120101000001Core.php',
			'Migration20120101000002Core.php',
			'Migration20120101000003Core.php',
			'Migration20120101000004Core.php',
			'Migration20120101000005Core.php',
			'Migration20120101000006Core.php'
		);

		// This is a little strange, but it's what we've got to work with...
		// Call migrate as dry run and get log of what would be run
		// (ignore callbacks to hide messages from dry run)
		$this->callback('migration', 'ignoreCallbacks');
		$this->callback('migration', 'migrate', array('up', false, true, true));
		$this->callback('migration', 'honorCallbacks');
		$logs = $this->callback('migration', 'get', array('log'));

		// Now loop over the results and see if any have already been run
		if ($logs && count($logs) > 0)
		{
			foreach ($logs as $log)
			{
				if (stripos($log['message'], 'would ignore up') !== false)
				{
					// If we're here, something has already been run,
					// therefore, we want to mark our old migrations as already having been run
					foreach ($files as $file)
					{
						$base = 'migrations';
						$path = $base . DS . $file;
						$hash = hash('md5', $file);

						// Mark as run
						$this->callback('migration', 'recordMigration', array($file, $base, $hash, 'up'));

						// Print message
						$this->callback('migration', 'log', array("Marking as run: up() in {$file}", 'success'));
					}

					// Only go through this once...i.e. we're done
					return;
				}
			}
		}
	}
}
