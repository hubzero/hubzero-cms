<?php 
namespace Components\Partners\Models;
use Date;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Session;
//needs to be required in admin!
class Partner extends Relational{
		/**
	 * The table namespace, access to the SQL database
	 *
	 * @var string
	 */
	protected $namespace = 'partner';

		/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'name';


		/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'notempty'
	);

		/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			//change from com_drwho&controller=characters
			$base = 'index.php?option=com_partners&controller=partners';
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&task=delete&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'view':
			case 'permalink':
			default:
				$link .= '&task=view&id=' . $this->get('id');
			break;
		}

		return $link;
	}
	//this function is for the about text box in the edit, display and site. the different cases are for the different pages
	//raw takes out the format tag and clean takes out the html tags
	public function about($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			//site view
			case 'parsed':
				$content = $this->get('about.parsed', null);

				if ($content === null)
				{
					$about = \Html::content('prepare', (string) $this->get('about', ''));

					$this->set('about.parsed', (string) $about);

					return $this->about($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->about('parsed'));
			break;
			//admin view
			case 'raw':
			default:
				$content = $this->get('about');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}

		/**
	 * Get the picture of the group logo
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @param   boolean  $serveFile  Serve file?
	 * @return  string
	 * @since   2.1.0
	 */
	public function picture( $thumbnail=true, $serveFile=true)
	{
		static $fallback;

		if (!isset($fallback))
		{
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64' style='stroke-width: 0px; background-color: #ffffff;'>" .
					"<path fill='#d9d9d9' d='M63.9 64v-3c-.6-.9-1-1.8-1.4-2.8l-1.2-3c-.4-1-.9-1.9-1.4-2.8S58.8 50.9 58 " .
					"50c-.8-.8-1.5-1.3-2.4-1.5-.6-.2-1.1-.3-1.7-.4-.6 0-2.1-.3-4.4-.6l-8.4-1.3c-.2-.8-.4-1.5-.5-2.4-.1-" .
					".8-.3-1.5-.6-2.4.3-.6.7-1 1.1-1.5.4-.6.8-1 1.1-1.5.4-.6.7-1.3 1-2.2.3-.8.8-3.5 1.3-7.8l.4-3c.1-.9." .
					"1-1.4.1-1.5 0-2.9-1-5.6-3.1-8-1-1.3-2.4-2.4-4.1-3.2-1.8-.9-3.7-1.4-6-1.4-2.2 0-4.3.4-6 1.3-1.8.9-3" .
					".1 2-4.2 3.2-1.1 1.3-1.8 2.6-2.3 4.1-.6 1.4-.7 2.5-.7 3.2 0 .7 0 1.5.1 2.3l.4 2.9.4 3.1.4 3.3c.2 1" .
					".1.7 2.4 1.5 3.7.3.6.7 1.1 1.1 1.5l1.1 1.5c-.2.8-.4 1.5-.6 2.4-.1.8-.3 1.5-.6 2.4l-5.6.8-4.6.8c-1." .
					"2.2-2.1.3-2.6.4-.6.1-1.1.2-1.7.4-2.1.8-4 3.1-5.7 6.8L.9 58.5c-.4 1-.8 1.9-1.3 2.8V64h64.3z'/>" .
					"</svg>";

			$fallback = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		if (!$this->get('id'))
		{
			return $fallback;
		}

		$picture = null;

		foreach (self::$pictureResolvers as $resolver)
		{
			$picture = $resolver->picture($this->get('id'), $this->get('name'), $this->get('email'), $thumbnail);

			if ($picture)
			{
				break;
			}
		}

		if (!$picture)
		{
			$picture = $fallback;
		}

		return $picture;
	}
		/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		return parent::destroy();
	}

	public function transformBackgroundImg()
	{
		$params = Component::params('com_partners');
		$base   = $params->get('image_location', DS . 'app' . DS . 'site' . DS . 'media' . DS . 'images' . DS . 'partners' . DS);

		return DS . trim($base, DS) . DS . $this->get('background_img');
	}
}