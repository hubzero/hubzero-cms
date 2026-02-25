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
 * @method static object addStyleSheet(string $url, string $type = 'text/css', string $media = null, array $attribs = [])
 * @method static object addScript(string $url, string $type = 'text/javascript', bool $defer = false, bool $async = false)
 * @method static object addStyleDeclaration(string $content, string $type = 'text/css')
 * @method static object addScriptDeclaration(string $content, string $type = 'text/javascript')
 * @method static object setMetaData(string $name, string $content, bool $http_equiv = false)
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
}
