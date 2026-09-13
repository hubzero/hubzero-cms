<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template;

use Hubzero\Container\Container;
use Hubzero\Config\Registry;
use Hubzero\Base\ClientManager;
use Hubzero\Database\Query;
use Exception;
use stdClass;

/**
 * Template loader class
 */
class Loader
{
    /**
     * The application implementation.
     *
     * @var  object
     */
    protected $app;

    /**
     * Base path for templates
     *
     * @var  array
     */
    protected $paths = array(
        'app'  => null,
        'core' => null
    );

    /**
     * Specified style
     *
     * @var  integer
     */
    protected $style = 0;

    /**
     * Language tag
     *
     * @var  string
     */
    protected $lang = '';

    /**
     * Constructor
     *
     * @param   object  $app
     * @param   array   $options
     * @return  void
     */
    public function __construct(Container $app, $options = array())
    {
        $this->app = $app;

        if (array_key_exists('style', $options)) {
            $this->setStyle($options['style']);
        }

        if (array_key_exists('lang', $options)) {
            $this->setLang($options['lang']);
        }

        if (array_key_exists('path_app', $options)) {
            $this->setPath('app', $options['path_app']);
        }

        if (array_key_exists('path_core', $options)) {
            $this->setPath('core', $options['path_core']);
        }
    }

    /**
     * Set path for a key
     *
     * @param   string  $key
     * @param   string  $path
     * @return  object
     */
    public function setPath($key, $path)
    {
        $this->paths[(string) $key] = (string) $path;

        return $this;
    }

    /**
     * Get path for key name
     *
     * @param   string  $key
     * @return  string
     */
    public function getPath($key)
    {
        return (isset($this->paths[$key]) ? $this->paths[$key] : '');
    }

    /**
     * Where a template of this name lives, app overriding core
     *
     * @param   string  $name  Template name
     * @return  string  The directory, or an empty string where there is none
     */
    public function pathFor($name)
    {
        $name = preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $name);

        if ($name === '') {
            return '';
        }

        foreach (array('app', 'core') as $key) {
            $path = $this->getPath($key) . DIRECTORY_SEPARATOR . $name;

            if (is_dir($path)) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Read a template's manifest without letting a bad one raise a warning
     *
     * @param   string  $path  The template's directory
     * @return  object  SimpleXMLElement, or null
     */
    protected function manifest($path)
    {
        $file = $path . DIRECTORY_SEPARATOR . 'templateDetails.xml';

        if (!is_file($file)) {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $manifest = simplexml_load_file($file);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $manifest ?: null;
    }

    /**
     * Work out whether a template inherits from another, and from where
     *
     * A child template names its parent in its own manifest:
     *
     *     <parent>lucent</parent>
     *
     * and the parent has to agree to it, with <inheritable>1</inheritable>.
     * Everything the child does not carry - the page shells, the component
     * overrides, the stylesheets and the scripts - is then looked for in the
     * parent, so a hub that wants its own colours and its own front page
     * ships those two files rather than a copy of the whole template.
     *
     * One generation only, as Joomla has it: a template that is itself a
     * child cannot be a parent, so there is no chain to walk and no way to
     * describe a loop.
     *
     * Sets parent and parentPath on the template, empty where there is none.
     *
     * @param   object  $template  The template to resolve
     * @return  void
     */
    protected function resolveParent($template)
    {
        $template->parent     = '';
        $template->parentPath = '';

        if (!($manifest = $this->manifest($template->path))) {
            return;
        }

        $parent = trim((string) $manifest->parent);

        if ($parent === '' || $parent === $template->template) {
            return;
        }

        if (!($path = $this->pathFor($parent))) {
            return;
        }

        if (!($parentManifest = $this->manifest($path))) {
            return;
        }

        // The parent must allow it, and must not be a child itself
        if (!(int) $parentManifest->inheritable
         || trim((string) $parentManifest->parent) !== '') {
            return;
        }

        $template->parent     = $parent;
        $template->parentPath = $path;
    }

    /**
     * Does a page shell of this name exist?
     *
     * The shell is the whole-page file that the tmpl request variable names -
     * index, component, group and the like. It is the active template's where
     * that template provides one, and the system template's otherwise, which
     * is the order the document looks in.
     *
     * Anything that switches the shell should ask this before it does. A name
     * neither template has is a 404, so switching to one blindly does not
     * degrade - it takes the page down, and if the switch is remembered in the
     * session it takes every later page down with it.
     *
     * @param   string  $name      Shell name, without the extension
     * @param   object  $template  The template to ask, or the active one
     * @return  bool
     */
    public function hasShell($name, $template = null)
    {
        $name = preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $name);

        if ($name === '') {
            return false;
        }

        $template = $template ?: $this->load();

        if (is_object($template)) {
            foreach (array('path', 'parentPath') as $key) {
                if (
                    !empty($template->$key)
                    && file_exists($template->$key . DIRECTORY_SEPARATOR . $name . '.php')
                ) {
                    return true;
                }
            }
        }

        return file_exists(
            $this->getPath('core') . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . $name . '.php'
        );
    }

    /**
     * Set style
     *
     * @param   integer  $style
     * @return  object
     */
    public function setStyle($style)
    {
        $this->style = (int) $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return  integer
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set language
     *
     * @param   string  $lang
     * @return  object
     */
    public function setLang($lang)
    {
        $this->lang = (string) $lang;

        return $this;
    }

    /**
     * Get language
     *
     * @return  string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Load a template by client
     *
     * @param   integer  $client_id  The client to load the tmeplate for
     * @return  string
     */
    public function load($client_id = null)
    {
        if (!is_null($client_id)) {
            $client = ClientManager::client($client_id, (! is_numeric($client_id)));
        } else {
            $client = $this->app['client'];
        }

        if (!$client) {
            throw new \InvalidArgumentException(sprintf('Invalid client type of "%s".', $client_id));
        }

        return $this->getTemplate((int)$client->id, $this->style);
    }

    /**
     * Get the system template
     *
     * @return  object
     */
    public function getSystemTemplate()
    {
        static $template;

        if (!isset($template)) {
            $template = new stdClass();
            $template->id        = 0;
            $template->home      = 0;
            $template->template  = 'system';
            $template->params    = new Registry();
            $template->protected = 1;
            $template->path      =  $this->getPath('core') . DIRECTORY_SEPARATOR . $template->template;
        }

        return $template;
    }

    /**
     * Build the template named by the config file for a client, without
     * touching the database
     *
     * Reads site_template (client 0) or administrator_template (client 1)
     * and returns a template object if that template exists on disk, app
     * directory first, then core.
     *
     * @param   integer  $client_id
     * @return  object|null
     */
    public function getConfiguredTemplate($client_id = 0)
    {
        if (!$this->app->has('config')) {
            return null;
        }

        $key  = ((int)$client_id === 1) ? 'administrator_template' : 'site_template';
        $name = $this->app['config']->get($key);

        if (!is_string($name) || $name === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $name)) {
            return null;
        }

        foreach (array('app', 'core') as $base) {
            $path = $this->getPath($base) . DIRECTORY_SEPARATOR . $name;

            if (file_exists($path . DIRECTORY_SEPARATOR . 'index.php')) {
                $template = new stdClass();
                $template->id        = 0;
                $template->home      = 1;
                $template->template  = $name;
                $template->params    = new Registry();
                $template->protected = 0;
                $template->path      = $path;

                return $template;
            }
        }

        return null;
    }

    /**
     * Get a list of templates for the specified client
     *
     * @param   integer  $client_id
     * @param   integer  $id
     * @return  object
     */
    public function getTemplate($client_id = 0, $id = 0)
    {
        if (!$this->app->has('cache.store') || !($cache = $this->app['cache.store'])) {
            $cache = new \Hubzero\Cache\Storage\None(array('hash' => $this->app->hash('template.loader')));
        }

        $templates = $cache->get('com_templates.templates' . $client_id . $this->lang);

        if (!$templates || empty($templates)) {
            try {
                $db = $this->app['db'];

                $s = '#__template_styles';
                $e = '#__extensions';

                $query = new Query($db);
                $query
                    ->select($s . '.id')
                    ->select($s . '.home')
                    ->select($s . '.template')
                    ->select($s . '.params')
                    ->select($e . '.protected')
                    ->from($s)
                    ->join($e, $e . '.element', $s . '.template')
                    ->whereEquals($s . '.client_id', (int)$client_id)
                    ->whereEquals($e . '.enabled', 1)
                    ->whereEquals($e . '.type', 'template')
                    ->whereRaw($db->quoteName($e . '.client_id') . ' = ' . $db->quoteName($s . '.client_id'));

                $query->order('home', 'desc');

                $db->setQuery($query->toString());
                $templates = $db->loadObjectList('id');

                foreach ($templates as $i => $template) {
                    $template->params = new Registry($template->params);

                    if (substr($template->template, 0, 4) == 'tpl_') {
                        $template->template = substr($template->template, 4);
                    }

                    if (is_dir($this->getPath('app') . DIRECTORY_SEPARATOR . $template->template)) {
                        $template->path = $this->getPath('app') . DIRECTORY_SEPARATOR . $template->template;
                    } else {
                        $template->path = $this->getPath('core') . DIRECTORY_SEPARATOR . $template->template;
                    }

                    $this->resolveParent($template);

                    $templates[$i] = $template;

                    // Create home element
                    if ($template->home && !isset($templates[0])) {
                        $templates[0] = clone $template;
                    }
                }

                $cache->put('com_templates.templates' . $client_id . $this->lang, $templates, $this->app['config']->get('cachetime', 15));
            } catch (Exception $e) {
                $templates = array();
            }
        }

        $tmpl = null;

        if (isset($templates[$id])) {
            $tmpl = $templates[$id];
        }

        // A template is usable when it has a page to render. A child template
        // need not carry one: inheriting index.php from its parent is the
        // whole point of being a child.
        if ($tmpl && (
            file_exists($tmpl->path . DIRECTORY_SEPARATOR . 'index.php')
            || (!empty($tmpl->parentPath)
                && file_exists($tmpl->parentPath . DIRECTORY_SEPARATOR . 'index.php'))
        )) {
            return $tmpl;
        }

        // Nothing usable came back for the default style: the database could
        // not answer, no style is marked as the default, or the one that is
        // names a template that is not installed. Fall back to the template
        // the config file records, which the template save action keeps
        // current. A request for one specific style is a narrower question
        // and still goes to the system template when it cannot be answered.
        if (!$id && ($configured = $this->getConfiguredTemplate($client_id))) {
            return $configured;
        }

        return $this->getSystemTemplate();
    }
}
