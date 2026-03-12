<?php
/**
 * BasicIndexAdapter — No-op index adapter for the "basic" search engine.
 *
 * The "basic" engine does not maintain a separate search index; results
 * come from direct database queries via component plugins.  This stub
 * prevents a fatal error when the engine config is set to "basic" and
 * code instantiates \Hubzero\Search\Index.
 *
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search\Adapters;

use Hubzero\Search\IndexInterface;

class BasicIndexAdapter implements IndexInterface
{
    public function __construct($config = null)
    {
        // No external service to connect to
    }

    public function getLogs()
    {
        return [];
    }

    public function lastInsert()
    {
        return null;
    }

    public function status()
    {
        return false;
    }

    public function index($document)
    {
        return $this;
    }

    public function delete($id)
    {
        return $this;
    }
}
