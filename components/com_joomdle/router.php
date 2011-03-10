<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: router.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
 */
function JoomdleBuildRoute(&$query) {
       $segments = array();
	
//	$menu = &JSite::getMenu();
	if (empty($query['Itemid'])) {

		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		}
		if(isset($query['cat_id']))
		{
			$segments[] = $query['cat_id'];
			unset( $query['cat_id'] );
		};
		if(isset($query['course_id']))
		{
			$segments[] = $query['course_id'];
			unset( $query['course_id'] );
		};
	} else {

		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		} 
		if(isset($query['cat_id']))
		{
			$segments[] = $query['cat_id'];
			unset( $query['cat_id'] );
		};
		if(isset($query['course_id']))
		{
			$segments[] = $query['course_id'];
			unset( $query['course_id'] );
		};
	} 
       return $segments;
}
/*
 * Function to convert a SEF URL back to a system URL
 */
function JoomdleParseRoute($segments) {
	$vars = array();

//	print_r ($segments);
        //Get the active menu item
        $menu =& JSite::getMenu();
        $item =& $menu->getActive();


        // Count route segments
        $count = count($segments);
        
      
       

	/* Esto lo usa el modulo */
     /*   if(!isset($item))
        {
	//	print_r ($segments);
                $vars['view']  = $segments[$count - 1];
		echo $vars['view'];
		if ($count == 3) {
			$vars['cat_id']    = $segments[$count - 3];
			$vars['course_id']    = $segments[$count - 2];
		} else if ($count == 2) {
			$vars['cat_id']    = $segments[$count - 2];
		}
                return $vars;
        } */
        //Standard routing for articles //XXX quitar?
	/*
	   XXX asi estaba en R0.24
        if(!isset($item))
        {
	//	print_r ($segments);
                $vars['view']  = $segments[0];
		if ($count == 3) {
			$vars['cat_id']    = $segments[$count - 2];
			$vars['course_id']    = $segments[$count - 1];
		} else if ($count == 2) {
			$vars['cat_id']    = $segments[$count - 1];
		}
                return $vars;
        }
*/
//echo "X".($item->query['view']).$count;

		if( !isset($item) )
        {
                if( $count > 0 )
                {
                        // If there are no menus we try to use the segments
                        $vars['view']  = $segments[0];

                        if(!empty($segments[1]))
                        {
                                $vars['task'] = $segments[1]; 
                        }
                        if (array_key_exists ($count - 2, $segments))
							$vars['cat_id']    = $segments[$count-2];
                        if (array_key_exists ($count - 1, $segments))
							$vars['course_id'] = $segments[$count-1];

				//		$vars['Itemid'] = 69;


					return $vars;
                }
        }

	

	if (!$item)
		$item->query['view'] = 'detail';
		

        switch($item->query['view'])
        {
                case 'coursecategories' :
                {
                        if($count == 2) {
                                $vars['view'] = 'coursecategory';
				$vars['cat_id']    = $segments[$count-1];
                        }

                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
				$vars['course_id']    = $segments[$count-1];
                        }


                } break;

                case 'mycourses'   :
                case 'teachers'   :
                case 'topics'   :
                case 'coursegradecategories'   :
                case 'coursesbycategory'   :
                case 'coursecategory'   :
                {
                        if($count == 2) {
                                $vars['view']  = 'detail';
				$vars['cat_id']    = $segments[$count-2];
                                $vars['course_id'] = $segments[$count-1];
                        }

                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
				$vars['course_id']    = $segments[$count-1];
                        }

                } break;

                case 'joomdle'   :
                {
                        if($count == 2) { // Usado por el pathway
                                $vars['view']  = 'coursecategory';
				$vars['cat_id']    = $segments[$count-1];
                        }

                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
				$vars['course_id']    = $segments[$count-1];
                        }

                } break;

                case 'detail'   :
                {
                        if($count == 2) {
                                $vars['view']  = 'detail';
								$vars['cat_id']    = $segments[$count-2];
                                $vars['course_id'] = $segments[$count-1];
                        }
                        if($count == 3) {
                               // $vars['view']  = 'teachers', topics, coursestats, coursegradecategories;
                                $vars['view']  = $segments[$count-3];
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
                        }

                } break;

                case 'mycoursegrades' :
                {
                        if($count == 3) {
                                $vars['view']  = 'coursegrades';
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
						}
                } break;

                case 'stats' :
                {
                        if($count == 3) {
                                $vars['view']  = 'coursestats';
                                $vars['cat_id'] = $segments[$count-2];
								$vars['course_id']    = $segments[$count-1];
						}
                } break;
                case 'assignment' :
                {
                	
                } break;
                
        }

	return $vars;

}
?>
