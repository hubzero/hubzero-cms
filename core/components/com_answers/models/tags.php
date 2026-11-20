<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Models;

use Components\Tags\Models\Cloud;

/**
 * Answers Tagging class
 */
class Tags extends Cloud
{
    /**
     * Object type, used for linking objects (such as resources) to tags
     *
     * @var  string
     */
    protected $scope = 'answers';

    /**
     * Turn a comma-separated string of tags into an array of normalized tags
     *
     * @param   string|array<mixed>   $tag_string  Comma-separated string of tags, or a list of them
     * @param   integer  $keep        Use normalized tag as array key
     * @return  array<mixed>
     */
    public function parse($tag_string, $keep = 0)
    {
        return $this->splitTags($tag_string, $keep);
    }
}
