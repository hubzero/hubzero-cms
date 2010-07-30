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

class ContribProjectHtml
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
        print "<h1>Contribute NEES Project: Create New Project</h1>";
?>
		<br/>
		<br/>
		<br/>
			
		<table>
		<tr>
		<td width="1000">
      <div class="section">

		<div class="subject contracted">
		
		<form action="." method="post" id="hubForm">
			<fieldset>
			<br/><br/>
			<strong>Project Name</strong>: <font color="red" size="1">REQUIRED</font>
			<input type="text" name="projName" />
			<font color="gray">Short name, used for the folder containing this project. Example: slick</font> 		
			 
            <br/><br/>
            
			<strong>Project Title</strong>:  <font color="red" size="1">REQUIRED</font>
			<input type="text" name="projTitle" />
			<font color="gray">Full name for this project. Example: Collaborative Research: Dynamic Behavior of Slickensided Surfaces</font>
			
            <br/><br/>
                        
			<strong>At a glance</strong>:  <font color="red" size="1">REQUIRED</font>  
			<input type="text" name="projGlance" />
			<font color="gray">A one line description of your project. Example: Two geotechnical centifuge tests were performed at the University of California, Davis.</font>
			
			
			<br/><br/>
                        
			<strong>Research Data Access Level</strong>: <font color="red" size="1">REQUIRED</font> 
						<select name="projAccess" >
							<option>-Select research data access level-</option>
							<option>Open to public</option>
							<option>Restricted to NEES Community</option>
							<option>Restricted to group(s)</option>
							<option>Restricted to research team</option>
						</select>
			<font color="gray">Open to the public or restricted to the research team.</font>
                        <br/><br/>

			<strong>Group Data Access Level</strong>: <font color="red" size="1">REQUIRED</font> 
						<select name="projAccess" >
							<option>-Select group data access level-</option>
							<option>Open to public</option>
							<option>Restricted to NEES Community</option>
							<option>Restricted to group(s)</option>
							<option>Restricted to research team</option>
						</select>
			<font color="gray">Open to the public or restricted to the research team.</font>
                        <br/><br/>
			<strong>Research Team</strong>:  <font color="red" size="1">REQUIRED</font>
			
			<input type="text" name="projUsers" />
			
			<font color="gray">NEEShub logins for people allowed to access and modify the project. Example: mylogin, fred, barney, wilma</font> 
                        <br/><br/>

			<input type="hidden" name="option" value="com_contribproject" />
			<input type="hidden" name="task" value="dosubmit" />
			<p class="submit"><input type="submit" name="submit" value="Submit" /></p>
			</fieldset>
		</form>
		</div>
		</div>
		</td>
		<td>
		<h3>How do I contribute a project?</h3>
We've tried to make the project contribution process easy. View resources explaining contribution steps.

<br/><br/>
		<h3>What project name should I choose?</h3>
Project name should be unique and contain 3-15 alphanumeric characters, no spaces. Once you register your project, you cannot change its name, so be careful to pick a good one.

<br/><br/>
		<h3>What is Research Data Access Level?</h3>
Research Level Access controls who is allowed to access, view, add, modify and delete files in your project before it is published to the project warehouse. 

<br/><br/>
		<h3>What is Group Data Access Level?</h3>
Group Level Access controls who is allowed to access and view files in your project before it is published to the project warehouse. 

		
		</td>
		</tr>
		</table>
		

<?
        
    }
    
    public function projCreated($cmd) {
    	print  $cmd;
//    	$keys = array_keys($res);

//    	foreach($res as $val) {
////    		print "<p>" . $key . "</p>";
//			var_dump($val);	
//    	}
    	
//    	while($row = mysql_fetch_assoc($res)) {
//    		var_dump($row) . "<br/><br/><br/>";
//    	}
//    	print "<h3>SVN repo : http://todo/" . $projName . "<h3>";
    }
    
}
?>
