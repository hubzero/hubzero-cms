<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Content\Site\Helpers;

use Component;

/**
 * Content Component Query Helper
 */
class Query
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param   string  $orderby  The ordering code.
	 * @return  string  The SQL field(s) to order by.
	 */
	public static function orderbyPrimary($orderby)
	{
		switch ($orderby)
		{
			case 'alpha':
				$orderby = array('c.path', 'asc');
				break;

			case 'ralpha':
				$orderby = array('c.path', 'desc');
				break;

			case 'order':
				$orderby = array('c.lft', 'asc');
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary category ordering.
	 *
	 * @param   string  $orderby    The ordering code.
	 * @param   string  $orderDate  The ordering code for the date.
	 * @return  string  The SQL field(s) to order by.
	 */
	public static function orderbySecondary($orderby, $orderDate = 'created')
	{
		$queryDate = self::getQueryDate($orderDate);

		switch ($orderby)
		{
			case 'date' :
				$orderby = array($queryDate, 'asc');
				break;

			case 'rdate' :
				$orderby = array($queryDate, 'DESC');
				break;

			case 'alpha' :
				$orderby = array('a.title', 'asc');
				break;

			case 'ralpha' :
				$orderby = array('a.title', 'DESC');
				break;

			case 'hits' :
				$orderby = array('a.hits', 'DESC');
				break;

			case 'rhits' :
				$orderby = array('a.hits', 'asc');
				break;

			case 'order' :
				$orderby = array('a.ordering', 'asc');
				break;

			case 'author' :
				$orderby = array('author', 'asc');
				break;

			case 'rauthor' :
				$orderby = array('author', 'DESC');
				break;

			case 'front' :
				$orderby = array('a.featured', 'DESC'); //, fp.ordering';
				break;

			default :
				$orderby = array('a.ordering', 'asc');
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param   string  $orderDate  The ordering code.
	 * @return  string  The SQL field(s) to order by.
	 */
	public static function getQueryDate($orderDate)
	{
		switch ($orderDate)
		{
			case 'modified' :
				$queryDate = 'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END';
				break;

			// use created if publish_up is not set
			case 'published' :
				$queryDate = 'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END';
				break;

			case 'created' :
			default :
				$queryDate = 'a.created';
				break;
		}

		return $queryDate;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param   object  $param  An options object for the article.
	 * @return  array   A named array with "select" and "join" keys.
	 */
	public static function buildVotingQuery($params=null)
	{
		if (!$params)
		{
			$params = Component::params('com_content');
		}

		$voting = $params->get('show_vote');

		if ($voting)
		{
			// calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__content_rating AS v ON a.id = v.content_id';
		}
		else
		{
			$select = '';
			$join = '';
		}

		$results = array(
			'select' => $select,
			'join'   => $join
		);

		return $results;
	}

	/**
	 * Method to order the intro articles array for ordering
	 * down the columns instead of across.
	 * The layout always lays the introtext articles out across columns.
	 * Array is reordered so that, when articles are displayed in index order
	 * across columns in the layout, the result is that the
	 * desired article ordering is achieved down the columns.
	 *
	 * @param   array    $articles    Array of intro text articles
	 * @param   integer  $numColumns  Number of columns in the layout
	 * @return  array    Reordered array to achieve desired ordering down columns
	 */
	public static function orderDownColumns(&$articles, $numColumns = 1)
	{
		$count = count($articles);

		// just return the same array if there is nothing to change
		if ($numColumns == 1 || !is_array($articles) || $count <= $numColumns)
		{
			$return = $articles;
		}
		// we need to re-order the intro articles array
		else
		{
			// we need to preserve the original array keys
			$keys = array_keys($articles);

			$maxRows = ceil($count / $numColumns);
			$numCells = $maxRows * $numColumns;
			$numEmpty = $numCells - $count;
			$index = array();

			// calculate number of empty cells in the array

			// fill in all cells of the array
			// put -1 in empty cells so we can skip later
			for ($row = 1, $i = 1; $row <= $maxRows; $row++)
			{
				for ($col = 1; $col <= $numColumns; $col++)
				{
					if ($numEmpty > ($numCells - $i))
					{
						// put -1 in empty cells
						$index[$row][$col] = -1;
					}
					else
					{
						// put in zero as placeholder
						$index[$row][$col] = 0;
					}
					$i++;
				}
			}

			// layout the articles in column order, skipping empty cells
			$i = 0;
			for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
			{
				for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
				{
					if ($index[$row][$col] != - 1)
					{
						$index[$row][$col] = $keys[$i];
						$i++;
					}
				}
			}

			// now read the $index back row by row to get articles in right row/col
			// so that they will actually be ordered down the columns (when read by row in the layout)
			$return = array();
			$i = 0;
			for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
			{
				for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
				{
					$return[$keys[$i]] = $articles[$index[$row][$col]];
					$i++;
				}
			}
		}

		return $return;
	}
}
