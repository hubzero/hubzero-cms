<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Document facade
 *
 * @method static object instance(string $type = null, array $options = [])
 * @method static object setType(string $type)
 * @method static string getType()
 * @method static array  getTypes()
 * @method static object setTitle(string $title)
 * @method static string getTitle()
 * @method static object setDescription(string $description)
 * @method static string getDescription()
 * @method static object addStyleDeclaration(string $content, string $type = 'text/css')
 * @method static object addScriptDeclaration(string $content, string $type = 'text/javascript')
 * @method static object setMetaData(string $name, string $content, bool $http_equiv = false, bool $sync = true)
 * @method static object setCharset(string $type = 'utf-8')
 * @method static string getLanguage()
 * @method static object setLink(string $url)
 * @method static mixed  getBuffer(string $type = null, string $name = null, array $attribs = [])
 * @method static object setBuffer(mixed $content, mixed $options = [], string $name = null)
 * @method static object addCustomTag(string $html)
 * @method static object addHeadLink(string $href, string $relation, string $relType = 'rel', array $attribs = [])
 * @method static array  getHeadData()
 * @method static object addItem(\Hubzero\Document\Type\Feed\Item $item)
 *
 * @codeCoverageIgnore
 */
class Document extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'document';
    }

    /**
     * Adds a linked stylesheet to the page
     *
     * Declared here rather than as a tag because its signature does not fit
     * on one line, and a tag cannot be wrapped.
     *
     * @param   string       $url      URL to the linked style sheet
     * @param   string       $type     Mime encoding type
     * @param   string|null  $media    Media type that this stylesheet applies to
     * @param   array        $attribs  Array of attributes
     * @return  object       The document, to allow chaining
     */
    public static function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = [])
    {
        return static::getRoot()->addStyleSheet(...func_get_args());
    }

    /**
     * Adds a linked script to the page
     *
     * Declared here rather than as a tag because its signature does not fit
     * on one line, and a tag cannot be wrapped.
     *
     * @param   string   $url    URL to the linked script
     * @param   string   $type   Type of script
     * @param   boolean  $defer  Adds the defer attribute
     * @param   boolean  $async  Adds the async attribute
     * @return  object   The document, to allow chaining
     */
    public static function addScript($url, $type = 'text/javascript', $defer = false, $async = false)
    {
        return static::getRoot()->addScript(...func_get_args());
    }
}
