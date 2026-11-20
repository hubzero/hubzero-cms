<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        // What a database or PHP error says (the SQL, table and column names,
        // file paths) is for the log; a client sees it only with debug on.
        // Messages the API throws on purpose are left as they are.
        if (
            !$this->debug
            && ($error instanceof \Hubzero\Database\Exception\QueryFailedException
                || $error instanceof \PDOException
                || $error instanceof \Error)
        ) {
            $content->message = Response::$statusTexts[$status];
        }
        $content->code    = $status;

        $this->response->setStatusCode($content->code);
        $this->response->setContent($content);
        $this->response->send();

        exit();
    }
}
