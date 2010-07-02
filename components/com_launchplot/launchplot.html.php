<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined("n")) {
    define("t","\t");
    define("n","\n");
    define("br","<br />");
    define("sp","&#160;");
    define("a","&amp;");
}

class LaunchAuthorHtml
{

    public function error( $msg, $tag='p' )
    {
        return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
    }



    public function generateTimeStampInput($id)
    {

        return $html;
    }

    //-----------

    public function warning( $msg, $tag='p' )
    {
        return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
    }

    //-----------

    public function div( $txt, $cls='', $id='' )
    {
        $html  = '<div';
        $html .= ($cls) ? ' class="'.$cls.'"' : '';
        $html .= ($id) ? ' id="'.$id.'"' : '';
        $html .= '>';
        $html .= ($txt != '') ? n.$txt.n : '';
        $html .= '</div><!-- / ';
        if ($id) {
            $html .= '#'.$id;
        }
        if ($cls) {
            $html .= '.'.$cls;
        }
        $html .= ' -->'.n;
        return $html;
    }

    //-----------

    public function hed( $level, $words, $class='' )
    {
        $html  = '<h'.$level;
        $html .= ($class) ? ' class="'.$class.'"' : '';
        $html .= '>'.$words.'</h'.$level.'>'.n;
        return $html;
    }

    //-----------

    public function start($_GET)
    {
        print "<h1>Launch Author</h1>";

        
    }
}
?>
