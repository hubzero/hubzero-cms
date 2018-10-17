<?php 
namespace Components\Partners\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Session;
use Date;
use stdClass;

//needs to be required in admin!
class Partner extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * The table namespace, access to the SQL database
	 *
	 * @var string
	 */
	protected $namespace = 'partner';

	/**
	 * Default order-by for model
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

	/**
	 * Get the partner's bio
	 * 
	 * @param  string  $as      Format to return state in [text, number]
	 * @param  integer $shorten Number of characters to shorten text to
	 * @return string
	 */
	public function about($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			// site view
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

			// admin view
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
	 * Returns partner_type object
	 * 
	 * @return object Partner_type object of partner
	 */
	public function partner_type()
	{
		return Partner_type::oneOrFail($this->get('partner_type'));
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

		return DS . trim($base, DS) . DS . $this->get('logo_img');
	}
	
	/*
	 * Namespace used for solr Search
	 * @return string
	 */
	public static function searchNamespace()
	{
		$searchNamespace = 'partner';
		return $searchNamespace;
	}
	
	/*
	 * Generate solr search Id
	 * @return string
	 */
	public function searchId()
	{
		$searchId = self::searchNamespace() . '-' . $this->get('id');
		return $searchId;
	}
	
	/**
	 * Get total number of records that will be indexed by Solr.
	 * @return integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}
	
	/**
	 * Get records
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return  object
	 */
	public static function searchResults($limit, $offset = 0)
	{
		return self::all()
			->start($offset)
			->limit($limit)
			->whereEquals('state', 1)
			->rows();
	}
	
	/*
	 * Generate search document for Solr
	 * @return array
	 */
	public function searchResult()
	{
		if ($this->get('state') == 0)
		{
			return false;
		}

		$obj = new stdClass;
		$obj->hubtype = self::searchNamespace();
		$obj->id = $this->searchId();
		$obj->title = $this->get('name');

		$description = $this->get('activities') . ' ' . $this->get('about');
		$description = html_entity_decode($description);
		$description = \Hubzero\Utility\Sanitize::stripAll($description);

		$obj->description   = $description;
		$obj->url = ($this->get('groups_cn') ? \Request::root() . 'groups/' . $this->get('groups_cn') : $this->get('site_url'));

		// No tags
		$obj->tags[] = array(
			'id' => '',
			'title' => ''
		);
		
		// Needed for admin database view
		$obj->access_level = 'public';
		$obj->owner_type = 'user';
		$obj->owner = '';
		
		return $obj;
	}
}
