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
     * @var  string
     */
    public $abstract;

    /**
     * Object description
     *
     * @var  string
     */
    public $description;

    /**
     * Object dc type
     *
     * @var  string
     */
    public $type;

    /**
     * URL
     *
     * @var  string
     */
    public $url;

    /**
     * DOI
     *
     * @var  string
     */
    public $doi;

    /**
     * Publisher
     *
     * @var  string
     */
    public $publisher;

    /**
     * Journal
     *
     * @var  string
     */
    public $journal;

    /**
     * Subject
     *
     * @var  string
     */
    public $subject;

    /**
     * Language
     *
     * @var  string
     */
    public $language;

    /**
     * Format
     *
     * @var  string
     */
    public $format;

    /**
     * Date
     *
     * @var  string
     */
    public $date;

    /**
     * Date
     *
     * @var  string
     */
    public $issued;

    /**
     * Volume
     *
     * @var  string
     */
    public $volume;

    /**
     * Issue
     *
     * @var  string
     */
    public $issue;

    /**
     * Page
     *
     * @var  string|null
     */
    public $page = null;

    /**
     * ISBN
     *
     * @var  string
     */
    public $isbn;

    /**
     * Author
     *
     * @var  string
     */
    public $author;
}
