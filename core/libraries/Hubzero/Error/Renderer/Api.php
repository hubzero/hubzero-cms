<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Http\Response;

/**
 * Displays plain error info when an uncaught exception occurs.
 */
class Api implements RendererInterface
{
    /**
     * Response object
     *
     * @var  \Hubzero\Api\Response
     */
    protected $response;

    /**
     * Debugging turned on?
     *
     * @var  bool
     */
    protected $debug;

    /**
     * Create a new exception renderer.
     *
     * @param   \Hubzero\Api\Response $response  The API client's response, which formats objects
     * @param   bool    $debug
     * @return  void
     */
    public function __construct(Response $response, $debug = false)
    {
        $this->response = $response;
        $this->debug    = $debug;
    }

    /**
     * Display the given exception to the user.
     *
     * @param   \Throwable  $error
     * @return  void
     */
    public function render($error)
    {
        $status = $error->getCode() ? $error->getCode() : 500;
        // Only a real HTTP status, not an exception's own code
        $status = isset(Response::$statusTexts[$status]) ? $status : 500;

        $content = new \StdClass();
        $content->message = $error->getMessage();
        $content->code    = $status;

        $this->response->setStatusCode($content->code);
        $this->response->setContent($content);
        $this->response->send();

        exit();
    }
}
