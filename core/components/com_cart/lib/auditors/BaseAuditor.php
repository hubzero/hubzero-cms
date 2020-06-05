<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Auditors;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';

class BaseAuditor
{

	public function __construct($type, $pId = null, $crtId = null)
	{
		$this->type = $type;
		$this->pId = $pId;
		$this->crtId = $crtId;

		// Get user, if any
		$this->uId = \Components\Cart\Models\Cart::getCartUser($crtId);

		// Set up the default empty ok response
		$this->response = new \stdClass();
		$this->response->status = 'ok';
		$this->response->messages = array();
		$this->response->notices = array();
		$this->response->errors = array();
	}

	public function setSku($sId)
	{
		$this->sId = $sId;
	}

	public function getSku()
	{
		if (!empty($this->sId) && $this->sId)
		{
			return $this->sId;
		}
		return false;
	}

	public function audit()
	{
		return $this->getResponse();
	}

	protected function setResponseStatus($status)
	{
		$this->response->status = $status;
	}

	protected function setResponseMessage($msg)
	{
		$this->response->messages[] = $msg;
	}

	protected function setResponseNotice($msg)
	{
		$this->response->notices[] = $msg;
	}

	// ok, notice, error
	protected function setResponseError($msg)
	{
		$this->response->errors[] = $msg;
	}

	public function getResponseError()
	{
		if (count($this->response->errors) > 0)
		{
			return $this->response->errors[0];
		}
		return false;
	}

	protected function getResponse()
	{
		return $this->response;
	}
}
