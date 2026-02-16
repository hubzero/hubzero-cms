<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Processor;

use Hubzero\Config\Exception\ParseException;
use Hubzero\Config\Processor as Base;
use stdClass;

/**
 * YAML Processor
 *
 * Uses the PECL yaml extension (yaml_parse/yaml_emit)
 */
class Yaml extends Base
{
    /**
     * Returns an array of allowed file extensions for this parser
     *
     * @return  array
     */
    public function getSupportedExtensions()
    {
        return array('yaml', 'yml');
    }

    /**
     * Loads a YAML/YML file as an array
     *
     * @param   string  $path
     * @return  array
     * @throws  ParseException If there is an error parsing the YAML file
     */
    public function parse($path)
    {
        $data = @\yaml_parse_file($path);

        if ($data === false) {
            throw new ParseException(
                array(
                    'message' => 'Error parsing YAML file: ' . $path,
                    'file' => $path,
                )
            );
        }

        return $data;
    }

    /**
     * Try to determine if the data can be parsed
     *
     * @param   string   $data
     * @return  boolean
     */
    public function canParse($data)
    {
        $data = trim($data);

        $parsed = @\yaml_parse($data);

        if ($parsed === false) {
            return false;
        }

        return true;
    }

    /**
     * Converts an object into a YAML formatted string.
     *
     * @param   object  $object   Data source object.
     * @param   array   $options  Options used by the formatter.
     * @return  string  YAML formatted string.
     */
    public function objectToString($object, $options = array())
    {
        if (is_string($object)) {
            return $object;
        }

        return \yaml_emit((array) $this->asArray($object));
    }

    /**
     * Method to recursively convert an object of data to an array.
     *
     * @param   object  $data  An object of data to return as an array.
     * @return  array   Array representation of the input object.
     */
    protected function asArray($data)
    {
        $array = array();

        foreach (get_object_vars((object) $data) as $k => $v) {
            if (is_object($v)) {
                $array[$k] = $this->asArray($v);
            } else {
                $array[$k] = $v;
            }
        }

        return $array;
    }

    /**
     * Parse a YAML formatted string and convert it into an object.
     *
     * @param   string  $data     YAML formatted string to convert.
     * @param   array   $options  Options used by the formatter.
     * @return  object  Data object.
     */
    public function stringToObject($data, $options = array())
    {
        if (is_object($data)) {
            return $data;
        }

        $data = trim($data);

        $parsed = @\yaml_parse($data);

        if ($parsed === false) {
            throw new ParseException(
                array(
                    'message' => 'Error parsing YAML',
                )
            );
        }

        if (!$parsed) {
            $parsed = '';
        }

        return (is_string($parsed) ? $parsed : $this->toObject($parsed));
    }

    /**
     * Convert an array to an object
     *
     * @param   array   $data
     * @return  object  Data object.
     */
    protected function toObject($data)
    {
        $obj = new stdClass();

        foreach ($data as $key => $datum) {
            if (is_array($datum)) {
                $obj->$key = $this->toObject($datum);
            } else {
                $obj->$key = $datum;
            }
        }

        return $obj;
    }
}

