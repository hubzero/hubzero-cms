<?php
namespace Components\Partners\Models;
use Hubzero\Database\Relational;
use Session;
use Date;

//
/**
 * Partners model class for partner_types
 */
class Partner_type extends Relational
{
	/**
	 * The table namespace, for the database
	 *
	 * @var  string
	 **/
	protected $namespace = 'partner_type';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'internal' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/

	/**
	
	
	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params String or array of extra params to append
	 * @return     string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			//this is important for accessing table
			$base = 'index.php?option=com_partners';
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&controller=partner_types&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&controller=partner_types&task=delete&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'view':
			case 'permalink':
			default:
				$link .= '&controller=partners&partner_types=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Defines a one to many through relationship with records by way of tasks
	 *
	 * @return  $this
	 */
	public function partners()
	{
		return $this->oneToMany('Partner', 'partner_type');
	}

	//makes our description box text look better for the site, display or edit pages
	//raw takes out format comment and clean takes out html tags
	public function description($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			//site view
			case 'parsed':
				$content = $this->get('description.parsed', null);

				if ($content === null)
				{
					$description = \Html::content('prepare', (string) $this->get('description', ''));

					$this->set('description.parsed', (string) $description);

					return $this->description($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->description('parsed'));
			break;
			//admin view
			case 'raw':
			default:
				$content = $this->get('description');
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
