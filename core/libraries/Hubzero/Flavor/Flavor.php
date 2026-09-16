<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Flavor;

use InvalidArgumentException;

/**
 * A flavor: what a hub is shaped into, written as data
 *
 * A flavor names the levers to pull - components and modules to enable or
 * disable, parameters to set, a template to make the default, dashboard
 * tiles, knowledge base and content articles to publish or not, resource
 * types to open or close - and nothing else. What a lever does is the
 * Applier's business; a flavor only says which way it points.
 *
 * Flavors cascade. One that `extends` another starts from its parent's
 * levers and overrides them: a scalar replaces, a list of things to enable
 * removes them from the inherited list of things to disable (and the other
 * way round), parameters merge key by key, and a dashboard layout replaces
 * the whole layout, since a layout is one thing.
 **/
class Flavor
{
    /**
     * Every lever a flavor may pull, and the shape each takes
     *
     * A string names a scalar shape; an array names the keys allowed under
     * the lever and their shapes. Anything else in a definition is a
     * mistake, and is said to be one rather than silently ignored.
     *
     * @var  array
     */
    const LEVERS = array(
        'description'    => 'string',
        'extends'        => 'string',
        'template'       => 'string',
        'components'     => array('enable' => 'list', 'disable' => 'list'),
        'modules'        => array('enable' => 'list', 'disable' => 'list', 'params' => 'map'),
        'plugins'        => array('enable' => 'list', 'disable' => 'list', 'params' => 'map'),
        'dashboard'      => array('tiles' => 'tiles'),
        'kb'             => array('categories' => 'states', 'articles' => 'states'),
        'content'        => array('articles' => 'states'),
        'menu'           => array('items' => 'states'),
        'resource_types' => 'map',
    );

    /**
     * The flavor's name, the key it was defined under
     *
     * @var  string
     */
    protected $name;

    /**
     * The file it came from, for messages
     *
     * @var  string|null
     */
    protected $file;

    /**
     * The levers, as given (or as resolved, once cascaded)
     *
     * @var  array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param   string       $name
     * @param   array        $data
     * @param   string|null  $file
     * @throws  InvalidArgumentException  When a lever is not one there is
     */
    public function __construct($name, array $data, $file = null)
    {
        $this->name = (string) $name;
        $this->file = $file;
        $this->data = $data;

        $this->validate();
    }

    /**
     * The flavor's name
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * The file the flavor was defined in
     *
     * @return  string|null
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * The flavor's description, or an empty string
     *
     * @return  string
     */
    public function description()
    {
        return (string) $this->get('description', '');
    }

    /**
     * The flavor this one extends, if any
     *
     * @return  string|null
     */
    public function parent()
    {
        $parent = $this->get('extends');

        return ($parent !== null && $parent !== '') ? (string) $parent : null;
    }

    /**
     * One lever's setting, optionally one key under it
     *
     * @param   string  $lever
     * @param   mixed   $default
     * @param   string  $key
     * @return  mixed
     */
    public function get($lever, $default = null, $key = null)
    {
        if (!array_key_exists($lever, $this->data)) {
            return $default;
        }

        if ($key === null) {
            return $this->data[$lever];
        }

        return (is_array($this->data[$lever]) && array_key_exists($key, $this->data[$lever]))
            ? $this->data[$lever][$key]
            : $default;
    }

    /**
     * Every lever, as an array
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * This flavor cascaded onto its parent: the parent's levers, overridden
     *
     * The result has no `extends` of its own; the cascade is done.
     *
     * @param   Flavor  $parent
     * @return  Flavor
     */
    public function onto(Flavor $parent)
    {
        $merged = self::merge($parent->toArray(), $this->data);
        unset($merged['extends']);

        return new self($this->name, $merged, $this->file);
    }

    /**
     * A child's levers laid over a parent's
     *
     * @param   array  $parent
     * @param   array  $child
     * @return  array
     */
    public static function merge(array $parent, array $child)
    {
        $result = $parent;

        foreach ($child as $lever => $value) {
            switch ($lever) {
                case 'components':
                case 'modules':
                case 'plugins':
                    $result[$lever] = self::mergeSwitches(
                        isset($parent[$lever]) ? $parent[$lever] : array(),
                        $value
                    );
                    break;

                case 'kb':
                case 'content':
                case 'menu':
                    $result[$lever] = isset($parent[$lever]) ? $parent[$lever] : array();

                    foreach ($value as $key => $states) {
                        $result[$lever][$key] = array_merge(
                            isset($result[$lever][$key]) ? $result[$lever][$key] : array(),
                            $states
                        );
                    }
                    break;

                case 'resource_types':
                    $result[$lever] = isset($parent[$lever]) ? $parent[$lever] : array();

                    foreach ($value as $alias => $columns) {
                        $result[$lever][$alias] = array_merge(
                            isset($result[$lever][$alias]) ? $result[$lever][$alias] : array(),
                            $columns
                        );
                    }
                    break;

                default:
                    // description, extends, template, dashboard: the child's word
                    $result[$lever] = $value;
                    break;
            }
        }

        return $result;
    }

    /**
     * Merge an enable/disable/params lever
     *
     * A name the child enables leaves the inherited disable list, and the
     * other way round, so the child's word on each name is the last one.
     *
     * @param   array  $parent
     * @param   array  $child
     * @return  array
     */
    protected static function mergeSwitches(array $parent, array $child)
    {
        $enable  = isset($parent['enable']) ? $parent['enable'] : array();
        $disable = isset($parent['disable']) ? $parent['disable'] : array();

        if (isset($child['enable'])) {
            $disable = array_diff($disable, $child['enable']);
            $enable  = array_merge($enable, $child['enable']);
        }

        if (isset($child['disable'])) {
            $enable  = array_diff($enable, $child['disable']);
            $disable = array_merge($disable, $child['disable']);
        }

        $result = array();

        if ($enable) {
            $result['enable'] = array_values(array_unique($enable));
        }

        if ($disable) {
            $result['disable'] = array_values(array_unique($disable));
        }

        $params = isset($parent['params']) ? $parent['params'] : array();

        foreach (isset($child['params']) ? $child['params'] : array() as $element => $values) {
            $params[$element] = array_merge(isset($params[$element]) ? $params[$element] : array(), $values);
        }

        if ($params) {
            $result['params'] = $params;
        }

        return $result;
    }

    /**
     * Say so if a lever is not one there is, or is not shaped as it should be
     *
     * @return  void
     * @throws  InvalidArgumentException
     */
    protected function validate()
    {
        foreach ($this->data as $lever => $value) {
            if (!array_key_exists($lever, self::LEVERS)) {
                $this->complain(
                    "'{$lever}' is not a lever. The levers are: " . implode(', ', array_keys(self::LEVERS))
                );
            }

            $shape = self::LEVERS[$lever];

            if (!is_array($shape)) {
                $this->check($lever, $value, $shape);
                continue;
            }

            if (!is_array($value)) {
                $this->complain("'{$lever}' should hold " . implode(', ', array_keys($shape)));
            }

            foreach ($value as $key => $setting) {
                if (!array_key_exists($key, $shape)) {
                    $this->complain(
                        "'{$lever}.{$key}' is not a lever. Under '{$lever}' there is: "
                        . implode(', ', array_keys($shape))
                    );
                }

                $this->check($lever . '.' . $key, $setting, $shape[$key]);
            }
        }
    }

    /**
     * Check one setting against the shape it should have
     *
     * @param   string  $path   For the message
     * @param   mixed   $value
     * @param   string  $shape  string, list, map, states or tiles
     * @return  void
     */
    protected function check($path, $value, $shape)
    {
        switch ($shape) {
            case 'string':
                if (!is_string($value) && $value !== null) {
                    $this->complain("'{$path}' should be a string");
                }
                break;

            case 'list':
                if (!is_array($value) || $value !== array_filter($value, 'is_string')) {
                    $this->complain("'{$path}' should be a list of names");
                }
                break;

            case 'map':
                if (!is_array($value) || $value !== array_filter($value, 'is_array')) {
                    $this->complain("'{$path}' should map each name to its settings");
                }
                break;

            case 'states':
                if (!is_array($value) || $value !== array_filter($value, 'is_scalar')) {
                    $this->complain("'{$path}' should map each alias to a state");
                }
                break;

            case 'tiles':
                if (!is_array($value)) {
                    $this->complain("'{$path}' should be a list of tiles");
                }

                foreach ($value as $tile) {
                    if (!is_array($tile) || !isset($tile['module'], $tile['col'])) {
                        $this->complain("each tile in '{$path}' needs a module and a col");
                    }
                }
                break;
        }
    }

    /**
     * Throw, naming the flavor and its file
     *
     * @param   string  $what
     * @return  void
     * @throws  InvalidArgumentException
     */
    protected function complain($what)
    {
        throw new InvalidArgumentException(
            "Flavor '{$this->name}'" . ($this->file ? " in {$this->file}" : '') . ': ' . $what
        );
    }
}
