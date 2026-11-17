<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use Filesystem;

/**
 * Publications block elements class
 *
 */
class BlockElements extends Obj
{
    /**
     * Database
     *
     * @var  \Hubzero\Database\Driver
     */
    public $db = null;

    /**
     * Loaded elements
     *
     * @var  array<mixed>
     */
    protected $elements = [];

    /**
     * Directories, where block elements can be stored
     *
     * @var  array<mixed>
     */
    protected $path = [];

    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        $this->db = $db;
        $this->path[] = dirname(__FILE__) . DS . 'blockelement';
    }

    /**
     * Get status for a block element within publication
     *
     * @param   string  $name
     * @param   \stdClass|null  $manifest
     * @param   \Components\Publications\Models\Publication|null  $pub
     * @return  object
     */
    public function getStatus($name, $manifest = null, $pub = null)
    {
        // Load attachment type
        $element = $this->loadElement($name);

        if ($element === false || !$pub || !$pub->id) {
            $status = new \Components\Publications\Models\Status();
        } else {
            $status = $element->getStatus($manifest, $pub);
        }

        // Return status
        return $status;
    }

    /**
     * Draw element for
     *
     * @param   string   $name
     * @param   integer  $elementId
     * @param   string|null   $manifest
     * @param   \stdClass|null   $master
     * @param   \Components\Publications\Models\Publication|null  $pub
     * @param   object|null   $status
     * @param   string   $viewname
     * @param   integer  $order
     * @return  string|false
     */
    public function drawElement(
        $name,
        $elementId = 0,
        $manifest = null,
        $master = null,
        $pub = null,
        $status = null,
        $viewname = 'edit',
        $order = 0,
    ) {
        // Load attachment type
        $element = $this->loadElement($name);

        if ($element === false) {
            return false;
        } else {
            return $element->render($elementId, $manifest, $pub, $viewname, $status, $master, $order);
        }
    }

    /**
     * Get active element
     *
     * @param   array<mixed>   $elements
     * @param   object|mixed  $review
     * @return  array<string, mixed>
     */
    public function getActiveElement($elements, $review)
    {
        // What is the last incomplete element?
        $lastComplete   = 0;
        $lastIncomplete = 0;
        $total          = 0;
        $showElement    = 1;
        $collector      = [];
        $i              = 1;

        foreach ($elements as $elId => $el) {
            $collector[$i] = $elId;
            if ($el->status == 1) {
                $lastComplete = $i;
            }
            if (!$lastIncomplete) {
                // Curator review?
                if (
                    $review && $review->elements
                    && isset($review->elements->$elId) && $review->elements->$elId->status != 2
                ) {
                    $reviewStatus = $review->elements->$elId;
                    if ($reviewStatus->status == 0 && !$reviewStatus->lastupdate) {
                        $lastIncomplete = $i;
                    }
                    if ($reviewStatus->status == 1) {
                        $lastComplete = $i;
                    }
                } elseif ($el->status != 1) {
                    $lastIncomplete = $i;
                }
            }

            $total++;
            $i++;
        }

        $nextElement = isset($collector[$lastComplete + 1])
            ? $collector[$lastComplete + 1]
            : $collector[$lastComplete];

        if ($lastIncomplete) {
            $showElement = $collector[$lastIncomplete];
        } else {
            $showElement = isset($elements->$nextElement) ? $nextElement : $collector[$lastComplete];
        }

        return ['showElement' => $showElement, 'total' => $total];
    }

    /**
     * Loads a block
     *
     * @param   string   $name
     * @param   boolean  $new
     * @return  BlockElement\Dataselector|BlockElement\Metadata|false  False when there is no such type
     */
    public function loadElement($name, $new = false)
    {
        $signature = md5($name);

        if (
            (isset($this->elements[$signature])
            && !($this->elements[$signature] instanceof \__PHP_Incomplete_Class))
            && $new === false
        ) {
            return $this->elements[$signature];
        }

        $elementClass = __NAMESPACE__ . '\\BlockElement\\' . ucfirst($name);
        if (!class_exists($elementClass)) {
            return false;
        }

        $this->elements[$signature] = new $elementClass($this);
        return $this->elements[$signature];
    }
}
