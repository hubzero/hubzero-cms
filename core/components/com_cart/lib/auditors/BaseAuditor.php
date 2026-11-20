<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Auditors;

class BaseAuditor
{
    /**
     * @var  mixed
     */
    public $type;
    /**
     * @var  int|null
     */
    public $pId;
    /**
     * @var  int|null
     */
    public $crtId;
    /**
     * @var  int|false|null
     */
    public $uId;
    /** @var \stdClass */
    public $response;
    /**
     * @var  mixed
     */
    public $sId;

    /**
     * @param   mixed  $type
     * @param   int|null  $pId
     * @param   int|null  $crtId
     */
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
        $this->response->messages = [];
        $this->response->notices = [];
        $this->response->errors = [];
    }

    /**
     * @param   mixed  $sId
     * @return void
     */
    public function setSku($sId)
    {
        $this->sId = $sId;
    }

    /**
     * @return  mixed
     */
    public function getSku()
    {
        if (!empty($this->sId)) {
            return $this->sId;
        }
        return false;
    }

    /**
     * @return  mixed
     */
    public function audit()
    {
        return $this->getResponse();
    }

    /**
     * @param   string  $status
     * @return void
     */
    protected function setResponseStatus($status)
    {
        $this->response->status = $status;
    }

    /**
     * @param   mixed  $msg
     * @return void
     */
    protected function setResponseMessage($msg)
    {
        $this->response->messages[] = $msg;
    }

    /**
     * @param   string  $msg
     * @return void
     */
    protected function setResponseNotice($msg)
    {
        $this->response->notices[] = $msg;
    }

    // ok, notice, error
    /**
     * @param   string  $msg
     * @return void
     */
    protected function setResponseError($msg)
    {
        $this->response->errors[] = $msg;
    }

    /**
     * @return  mixed
     */
    public function getResponseError()
    {
        if (count($this->response->errors) > 0) {
            return $this->response->errors[0];
        }
        return false;
    }

    /**
     * @return  \stdClass|null
     */
    protected function getResponse()
    {
        return $this->response;
    }
}
