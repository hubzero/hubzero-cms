<?php
/**
* @copyright    Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license      GNU/GPL
*/
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

 
/**
 * Renders a multiple item select element
 *
 */
 
class JElementCoursecategorylist extends JElement
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        var    $_name = 'CourseCategoryList';
 
        function fetchElement($name, $value, &$node, $control_name)
        {
                // Base name of the HTML control.
                $ctrl  = $control_name .'['. $name .']';
 
		$options = $this->getCats (0);

                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = $node->attributes( 'size' )) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = $node->attributes( 'class' )) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = $node->attributes( 'multiple' ))
                {
                        $attribs       .= ' multiple="multiple"';
                        $ctrl          .= '[]';
                }
 
                // Render the HTML SELECT list.
                return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
        }

	function getCats ($cat_id, $options = array(), $level = 0)
        {
                $cats = JoomdleHelperContent::getCourseCategories ($cat_id);

                if (!is_array ($cats))
                        return $options;

                foreach ($cats as $cat)
                {
                        $val = $cat['id'];
                        $text = $cat['name'];
                        for ($i = 0; $i < $level; $i++)
                                $text = "&nbsp;".$text;
                        $options[] = JHTML::_('select.option', $val, $text);
                        $options = $this->getCats ($cat['id'], $options, $level + 1);
                }

                return $options;
        }

}

?>
