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

		if (array_key_exists('style', $options))
		{
			$this->setStyle($options['style']);
		}

		if (array_key_exists('lang', $options))
		{
			$this->setLang($options['lang']);
		}

		if (array_key_exists('path_app', $options))
		{
			$this->setPath('app', $options['path_app']);
		}

		if (array_key_exists('path_core', $options))
		{
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
		if (!is_null($client_id))
		{
			$client = ClientManager::client($client_id, (! is_numeric($client_id)));
		}
		else
		{
			$client = $this->app['client'];
		}

		if (!$client)
		{
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

		if (!isset($template))
		{
			$template = new stdClass;
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
	 * Get a list of templates for the specified client
	 *
	 * @param   integer  $client_id
	 * @param   integer  $id
	 * @return  object
	 */
	public function getTemplate($client_id = 0, $id = 0)
	{
		if (!$this->app->has('cache.store') || !($cache = $this->app['cache.store']))
		{
			$cache = new \Hubzero\Cache\Storage\None(array('hash' => $this->app->hash('template.loader')));
		}

		$templates = $cache->get('com_templates.templates' . $client_id . $this->lang);

		if (!$templates || empty($templates))
		{
			try
			{
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
					->whereRaw($e . '.`client_id` = `' . $s . '`.`client_id`');

				$query->order('home', 'desc');

				$db->setQuery($query->toString());
				$templates = $db->loadObjectList('id');

				foreach ($templates as $i => $template)
				{
					$template->params = new Registry($template->params);

					if (substr($template->template, 0, 4) == 'tpl_')
					{
						$template->template = substr($template->template, 4);
					}

					if (is_dir($this->getPath('app') . DIRECTORY_SEPARATOR . $template->template))
					{
						$template->path = $this->getPath('app') . DIRECTORY_SEPARATOR . $template->template;
					}
					else
					{
						$template->path = $this->getPath('core') . DIRECTORY_SEPARATOR . $template->template;
					}

					$templates[$i] = $template;

					// Create home element
					if ($template->home && !isset($templates[0]))
					{
						$templates[0] = clone $template;
					}
				}

				$cache->put('com_templates.templates' . $client_id . $this->lang, $templates, $this->app['config']->get('cachetime', 15));
			}
			catch (Exception $e)
			{
				$templates = array();
			}
		}

		$tmpl = null;

		if (isset($templates[$id]))
		{
			$tmpl = $templates[$id];
		}

		if ($tmpl && file_exists($tmpl->path . DIRECTORY_SEPARATOR . 'index.php'))
		{
			return $tmpl;
		}

		return $this->getSystemTemplate();
	}
}
