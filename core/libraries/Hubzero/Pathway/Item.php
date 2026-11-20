<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pathway;

/**
 * Pathway item
 */
class Item
{
    /**
     * Item url
     *
     * @var  string
     */
    public $link;

    /**
     * Item text
     *
     * @var  string
     */
    public $name;

    /**
     * Constructor
     *
     * @param   string  $name  The name of the item.
     * @param   string  $link  The link to the item.
     * @return  void
     */
    public function __construct($name = '', $link = '')
    {
        $this->name = (string) $name;
        $this->link = (string) $link;
    }
}
