<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import;

use Hubzero\Facades\Lang;

/**
 * Import Importer class
 */
class Importer
{
    /**
     * The adapter in use, once one is set or detected
     *
     * @var  \Components\Resources\Import\Interfaces\Adapter|null
     */
    private $adapter = null;

    /**
     * Array of Resource Import Adapters
     *
     * @var  list<class-string<Interfaces\Adapter>>
     */
    private $adapters;

    /**
     * Resource Import Repository Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        // get adapaters
        $this->bootAdapters();
    }

    /**
     * Get Instance of importer
     *
     * @param   mixed  $key   Instance Key
     * @return   mixed
     */
    public static function &getInstance($key = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self();
        }

        return $instances[$key];
    }

    /**
     * Method to boot import adapaters
     *
     * @return  void
     */
    private function bootAdapters()
    {
        // include all adapters
        foreach (glob(__DIR__ . DS . 'adapters' . DS . '*.php') ?: [] as $adapter) {
            require_once $adapter;
        }

        // set our adapters (any declared class implementing the adapter interface)
        $this->adapters = [];
        foreach (get_declared_classes() as $class) {
            if (is_a($class, Interfaces\Adapter::class, true)) {
                $this->adapters[] = $class;
            }
        }
    }

    /**
     * Method to set adapater
     *
     * @param   \Components\Resources\Import\Interfaces\Adapter $adapter
     * @return  void
     */
    public function setAdapter(\Components\Resources\Import\Interfaces\Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Method to get adapater
     *
     * @return  \Components\Resources\Import\Interfaces\Adapter  ResourcesImportInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Method auto detect adapter based on mime type
     *
     * @param   \Components\Resources\Models\Import $import
     * @return  void
     */
    private function autoDetectAdapter(\Components\Resources\Models\Import $import)
    {
        // make sure we dont already have an adapter
        if ($this->adapter) {
            return;
        }

        // get path to data file
        $dataPath = $import->getDataPath();

        // get the mime type of file
        $file  = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $file ? finfo_file($file, $dataPath) : false;

        // anonymous function to see if we can use any
        $respondsTo = function ($class) use ($mime) {
            return $class::accepts($mime);
        };

        // set the adapter if we found one
        $responded = array_filter($this->adapters, $respondsTo);

        if ($adapter = array_shift($responded)) {
            $this->setAdapter(new $adapter());
        }

        // do we still not have adapter
        if (!$this->adapter) {
            throw new \Exception(Lang::txt('Resource Import: No adapter found to count import data.'));
        }
    }

    /**
     * Count import data
     *
     * @param   \Components\Resources\Models\Import $import
     * @return  mixed
     */
    public function count(\Components\Resources\Models\Import $import)
    {
        // autodetect adapter
        $this->autoDetectAdapter($import);

        // call count on adapter
        return $this->adapterOrFail()->count($import);
    }

    /**
     * Process import data
     *
     * @param   \Components\Resources\Models\Import $import
     * @param   array<mixed>    $callbacks
     * @param   integer  $dryRun
     * @return  array<mixed>
     */
    public function process(\Components\Resources\Models\Import $import, array $callbacks, $dryRun = 1)
    {
        // autodetect adapter
        $this->autoDetectAdapter($import);

        // mark import run
        $import->markRun($dryRun);

        // call process on adapter
        return $this->adapterOrFail()->process($import, $callbacks, $dryRun);
    }

    /**
     * The adapter, which the calls using it need set
     *
     * @return  \Components\Resources\Import\Interfaces\Adapter
     * @throws  \LogicException  When it is not
     */
    protected function adapterOrFail()
    {
        if ($this->adapter === null) {
            throw new \LogicException(static::class . '::$adapter is not set');
        }

        return $this->adapter;
    }
}
