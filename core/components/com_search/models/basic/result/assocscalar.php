<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;

include_once dirname(__DIR__) . DS . 'result.php';

/**
 * Associative scalar result
 */
class AssocScalar extends SearchResult
{
    /**
     * Description for 'tag_weight_modifier'
     *
     * @var number
     */
    private static $tag_weight_modifier;

    /**
     * Description for 'row'
     *
     * @var unknown
     */
    private $row;

    /**
     * Short description for 'is_scalar'
     *
     * Long description (if any) ...
     *
     * @return     boolean Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function is_scalar()
    {
        return true;
    }

    /**
     * Short description for 'assert_keys'
     *
     * Long description (if any) ...
     *
     * @param   array    $keys Parameter description (if any) ...
     * @param   unknown  $row  Parameter description (if any) ...
     * @return  void
     * @throws  Exception
     */
    private static function assertKeys($keys, $row)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                throw new Exception("Result plugin did not define key '$key'");
            }
        }
    }

    /**
     * Short description for '__construct'
     *
     * Long description (if any) ...
     *
     * @param      array $row Parameter description (if any) ...
     * @return     void
     */
    public function __construct($row)
    {
        if (is_null(self::$tag_weight_modifier)) {
            self::$tag_weight_modifier = Set::get_tag_weight_modifier();
        }

        self::assertKeys(array('title', 'description', 'link'), $row);
        foreach ($row as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_map('stripslashes', array_map('strip_tags', $val));
            } else {
                $this->$key = stripslashes(strip_tags($val));
            }
        }

        if ($this->weight === null) {
            if ($this->tag_count) {
                $this->weight = $this->tag_count * (self::$tag_weight_modifier / 2);
            }
            $this->weight = 1.0;
            $this->weight_log[] = 'plugin did not suggest weight, guessing ' . $this->weight
                . ' based on tag count(' . $this->tag_count . ')';
        } elseif ($this->tag_count) {
            $this->weight_log[] = 'plugin suggested weight of ' . $this->weight;
            $this->adjust_weight($this->tag_count * self::$tag_weight_modifier, 'tag count of ' . $this->tag_count);
        }

        if ($this->contributors) {
            $contribs = is_array($this->contributors)
                ? $this->contributors : preg_split("#\n#", $row['contributors']);
            $this->contributors = array_unique($contribs);
        } else {
            $this->contributors = array();
        }
        if ($this->contributor_ids) {
            $contribIds = is_array($this->contributor_ids)
                ? $this->contributor_ids : preg_split("#\n#", $row['contributor_ids']);
            $this->contributor_ids = array_unique($contribIds);
        } else {
            $this->contributor_ids = array();
        }

        if ($this->date && $this->date != '0000-00-00 00:00:00') {
            $this->date = strtotime($row['date']);
        } else {
            $this->date = null;
        }
    }

    /**
     * Short description for 'get_result'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_result()
    {
        return $this->row;
    }

    /**
     * Short description for 'to_associative'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function to_associative()
    {
        return $this;
    }
}
