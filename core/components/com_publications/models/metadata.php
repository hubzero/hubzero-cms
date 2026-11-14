<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication DOI metadata base class
 */
class Metadata extends Obj
{
    /**
     *  Object title
     *
     * @var  string|null
     */
    public $title = null;

    /**
     * Object abstract
     *
     * @var  string|null
     */
    public $abstract = null;

    /**
     * Object description
     *
     * @var  string|null
     */
    public $description = null;

    /**
     * Object dc type
     *
     * @var  string|null
     */
    public $type = null;

    /**
     * URL
     *
     * @var  string|null
     */
    public $url = null;

    /**
     * DOI
     *
     * @var  string|null
     */
    public $doi = null;

    /**
     * Publisher
     *
     * @var  string|null
     */
    public $publisher = null;

    /**
     * Journal
     *
     * @var  string|null
     */
    public $journal = null;

    /**
     * Subject
     *
     * @var  string|null
     */
    public $subject = null;

    /**
     * Language
     *
     * @var  string|null
     */
    public $language = null;

    /**
     * Format
     *
     * @var  string|null
     */
    public $format = null;

    /**
     * Date
     *
     * @var  string|null
     */
    public $date = null;

    /**
     * Date
     *
     * @var  string|null
     */
    public $issued = null;

    /**
     * Volume
     *
     * @var  string|null
     */
    public $volume = null;

    /**
     * Issue
     *
     * @var  string|null
     */
    public $issue = null;

    /**
     * Page
     *
     * @var  string|null
     */
    public $page = null;

    /**
     * ISBN
     *
     * @var  string|null
     */
    public $isbn = null;

    /**
     * Author
     *
     * @var  string|null
     */
    public $author = null;
}
