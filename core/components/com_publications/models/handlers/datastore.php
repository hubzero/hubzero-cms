<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Handlers;

use Components\Publications\Models\Handler as Base;
use stdClass;

/**
 * DataStore Lite Handler
 */
class DataStore extends Base
{
	/**
	 * Handler type name
	 *
	 * @var  string
	 */
	protected $_name = 'datastore';

	/**
	 * Configs
	 *
	 * @var  object
	 */
	protected $_config = null;

	/**
	 * Get default params for the handler
	 *
	 * @param   array   $savedConfig
	 * @return  object
	 */
	public function getConfig($savedConfig = array())
	{
		// Defaults
		$configs = array(
			'name'   => 'datastore',
			'label'  => 'Data Viewer',
			'title'  => 'Interactive data explorer',
			'about'  => 'Selected CSV file will be viewed as a database',
			'params' => array(
				'allowed_ext'  => array('csv'),
				'required_ext' => array('csv'),
				'min_allowed'  => 1,
				'max_allowed'  => 1,
				'enforced'     => 0
			)
		);

		$this->_config = json_decode(json_encode($this->_parent->parseConfig($this->_name, $configs, $savedConfig)), false);
		return $this->_config;
	}

	/**
	 * Clean-up related files
	 *
	 * @param   string  $path
	 * @return  bool
	 */
	public function cleanup($path)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		return true;
	}

	/**
	 * Draw list of included items
	 *
	 * @param   array    $attachments
	 * @param   object   $attConfigs
	 * @param   object   $pub
	 * @param   boolean  $authorized
	 * @return  void
	 */
	public function drawList($attachments, $attConfigs, $pub, $authorized)
	{
		// No special treatment for this handler
		return;
	}

	/**
	 * Draw attachment
	 *
	 * @param   array   $data
	 * @param   object  $params
	 * @return  void
	 */
	public function drawAttachment($data, $params)
	{
		// No special treatment for this handler
		return;
	}

	/**
	 * Check for changed selections etc
	 *
	 * @param   array   $attachments
	 * @return  object
	 */
	public function getStatus($attachments)
	{
		// Start status
		$status = new \Components\Publications\Models\Status();
		return $status;
	}

	/**
	 * Draw handler status in editor
	 *
	 * @param   object  $editor
	 * @return  void
	 */
	public function drawStatus($editor)
	{
		return;
	}

	/**
	 * Draw handler editor content
	 *
	 * @param   object  $editor
	 * @return  void
	 */
	public function drawEditor($editor)
	{
		return;
	}

	/**
	 * Check against handler-specific requirements
	 *
	 * @param   array   $attachments
	 * @return  bool
	 */
	public function checkRequired($attachments)
	{
		return true;
	}
}
