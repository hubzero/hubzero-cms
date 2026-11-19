<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content;

use Hubzero\Content\Auditor\Test;
use Hubzero\Content\Auditor\Result;
use RuntimeException;

/**
 * Data auditor
 */
class Auditor
{
    /**
     * Holds registered tests
     *
     * @var  array<mixed>
     */
    protected $tests = [];

    /**
     * Holds list of detectors and what they reported
     *
     * @var  array<mixed>|null
     */
    protected $report = null;

    /**
     * Type of items being audited
     *
     * @var  string|null
     */
    protected $scope = null;

    /**
     * Log data?
     *
     * @var  \Hubzero\Log\Writer|null
     */
    protected $log = null;

    /**
     * Constructor
     *
     * @param   string  $scope
     * @param   \Hubzero\Log\Writer|null  $logger
     * @return  void
     */
    public function __construct($scope, $logger = null)
    {
        $this->scope = $scope;

        if ($logger) {
            $this->log = $logger;
        }
    }

    /**
     * Set logging
     *
     * @param   \Hubzero\Log\Writer  $logger
     * @return  void
     */
    public function setLogger($logger)
    {
        $this->log = $logger;
    }

    /**
     * Process data against a series of tests
     *
     * @param   string|array<mixed>  $data
     * @return  array<int, mixed>
     */
    public function check($data)
    {
        $results = [];

        $data = $this->prepareData($data);

        foreach ($data as $datum) {
            $results[] = [
                'data'  => $datum,
                'tests' => $this->process($datum),
            ];
        }

        if ($this->log) {
            $this->log($results);
        }

        return $results;
    }

    /**
     * Run the tests against a single data point
     *
     * @param   array<mixed>  $datum
     * @return  array<mixed>
     */
    public function process(array $datum)
    {
        $results = [];

        foreach ($this->tests as $key => $tester) {
            $result = $tester->examine($datum);
            $result->set('scope', $this->scope);
            $result->set('test_id', $key);

            $results[$tester->name()] = $result;
        }

        return $results;
    }

    /**
     * Register a test
     *
     * @param   \Hubzero\Content\Auditor\Test $test  TestInterface
     * @return  $this  Test
     * @throws  RuntimeException
     */
    public function registerTest(Test $test)
    {
        $key = $this->classSimpleName($test);

        if (isset($this->tests[$key])) {
            throw new RuntimeException(
                sprintf('Test [%s] already registered', $key),
            );
        }

        $this->tests[$key] = $test;

        return $this;
    }

    /**
     * Unregister a test
     *
     * @param   string  $key
     * @return  $this
     */
    public function unregisterTest($key)
    {
        $key = $this->classSimpleName($key);

        if (isset($this->tests[$key])) {
            unset($this->tests[$key]);
        }

        return $this;
    }

    /**
     * Gets a detector using its detector ID (Class Simple Name)
     *
     * @param   string  $key
     * @return  mixed   False or TestInterface
     */
    public function getTest($key)
    {
        if (!isset($this->tests[$key])) {
            return false;
        }

        return $this->tests[$key];
    }

    /**
     * Gets a list of all spam detectors
     *
     * @return  array<mixed>
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Get summations
     *
     * @return  array<mixed>
     */
    public function getReport()
    {
        if (!isset($this->report)) {
            $tests = [];

            foreach ($this->getTests() as $key => $audit) {
                if (!isset($tests[$key])) {
                    $tests[$key] = [
                        'name'   => $audit->name(),
                        'total'  => 0,
                        'totals' => [
                            'skipped' => 0,
                            'passed'  => 0,
                            'failed'  => 0,
                        ],
                    ];
                }

                $tests[$key]['totals']['skipped'] = Result::all()
                    ->whereEquals('scope', $this->scope)
                    ->whereEquals('test_id', $key)
                    ->whereEquals('status', 0)
                    ->total();

                $tests[$key]['totals']['passed'] = Result::all()
                    ->whereEquals('scope', $this->scope)
                    ->whereEquals('test_id', $key)
                    ->whereEquals('status', 1)
                    ->total();

                $tests[$key]['totals']['failed'] = Result::all()
                    ->whereEquals('scope', $this->scope)
                    ->whereEquals('test_id', $key)
                    ->whereEquals('status', -1)
                    ->total();

                $tests[$key]['total'] += $tests[$key]['totals']['skipped'];
                $tests[$key]['total'] += $tests[$key]['totals']['passed'];
                $tests[$key]['total'] += $tests[$key]['totals']['failed'];
            }

            $this->report = $tests;
        }

        return $this->report;
    }

    /**
     * Used to normalize string before passing
     * it to detectors
     *
     * @param   array<mixed>|string   $data
     * @return  array<mixed>
     */
    protected function prepareData($data)
    {
        if (is_string($data)) {
            $data = [$data];
        }

        return $data;
    }

    /**
     * Gets the name of a class (w. Namespaces removed)
     *
     * @param   mixed   $class  String (class name) or object
     * @return  string
     */
    protected function classSimpleName($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return str_replace('\\', '_', $class);
    }

    /**
     * Log results of the check
     *
     * @param   array<int, array<string, mixed>>  $report
     * @return  void
     */
    protected function log($report)
    {
        if (!$this->log) {
            return;
        }

        $this->log->info((string) json_encode($report));
    }
}
