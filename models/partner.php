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
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		return parent::destroy();
	}
}