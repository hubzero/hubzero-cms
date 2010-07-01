<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::back();

?>
<div class="message">Updated: 2004-11-13</div>

<h2>404 SEF for Mambo 4.5.1</h2>
<p><a href="http://mamboforge.net/projects/sef404/">http://mamboforge.net/projects/sef404/</a></p>

<h3>Project Summary</h3>

<p>Allows Search Engine Friendly URLS for apache and IIS, returns
proper 404 status code for missing content, provides logging of 404 errors, and
creation of special &quot;shortcut&quot; URLs that allow the user to redirection to the
new URL.</p>

<p>This package should contain the following files:</p>
<ul>
 <li><code>admin.sef.html.php</code></li>
 <li><code>admin.sef.php</code></li>
 <li><code>config.sef.php</code></li>
 <li><code>down.png</code></li>
 <li><code>index.html</code></li>
 <li><code>install.sef.php</code></li>
 <li><code>readme.inc</code></li>
 <li><code>sef.class.php</code></li>
 <li><code>sef.php</code></li>
 <li><code>sef404.php</code></li>
 <li><code>sef.xml</code></li>
 <li><code>toolbar.sef.html.php</code></li>
 <li><code>toolbar.sef.php</code></li>
 <li><code>uninstall.sef.php</code></li>
 <li><code>up.png</code></li>
</ul>

<h3>Project Documentation</h3>

<h4>Installation</h4>
<ol>
 <li>Upload the zip file to Mambo using the component installer in the usual way.</li>
 <li>For apache, add the following lines to your &quot;.htaccess&quot; file:<br />
	<code>
##<br />
# @package Mambo_4.5<br />
# @copyright (C) 2000 - 2004 Miro International Pty Ltd<br />
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL<br />
# Mambo is Free Software<br />
##<br />
<br />
#<br />
#  mod_rewrite in use<br />
#<br />
<br />
RewriteEngine On<br />
<br />
#  for apache on windows you might need to uncomment<br />
#  this Options line<br />
#Options +SymlinksIfOwnerMatch<br />
<br />
#  Uncomment following line if your webserver's URL <br />
#  is not directly related to physival file paths.<br />
#  Update YourMamboDirectory (just / for root)<br />
<br />
#RewriteBase /<br />
#<br />
#  Rules<br />
#<br />
<br />
RewriteCond %{REQUEST_FILENAME} !-f<br />
RewriteCond %{REQUEST_FILENAME} !-d<br />
RewriteRule ^(.*) /index.php<br />
	</code></li>
 <li>For IIS, see Configuring IIS..</li>
 <li>Ensure that SEF is enabled in the mambo backend.</li>
 <li>Edit the 404 SEF configuration, Change Enable to yes and save.<br/>
	This is neccessary to ensure the default 404 document gets saved to the mambo database.</li>
</ol>

<h4>Configuring IIS</h4>
<ol>
 <li><b>Install ActiveScript</b><br/>After installing PHP, you should download the ActiveScript DLL (php4activescript.dll)
	and place it in the main PHP folder (e.g. C:\php).<br/>
	<br/>After having all the files needed, you must register the DLL on your system.
	To achieve this, open a Command Prompt window (located in the Start Menu). Then
	go to your PHP directory by typing something like cd C:\php. To register the DLL
	just type <code>regsvr32 php4activescript.dll</code></li>
 <li><b>Install .NET framework 1.1</b><br/>To the best of my limited knowledge of IIS, this is required for web.config to
	work, so install it<br/><br/></li>
 <li><b>Create/Modify web.config</b><br><br/>
	<span class="disabled">NOTE: in the example below, mambo is installed in the virtual directory mambo</span><br/><br/>
	Create C:\Inetpub\wwwroot\web.config and add the content below:<br>
	<pre>&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;
&lt;configuration&gt;
&lt;system.web&gt;
&lt;compilation defaultLanguage=&quot;PHP4Script&quot; debug=&quot;true&quot; /&gt;
&lt;customerrors mode=&quot;On&quot; defaultRedirect=&quot;/mambo/index.php&quot; /&gt;
&lt;/system.web&gt;
&lt;/configuration&gt;</pre></li>
 <li><b>Configure the Custom Errors</b><br/><br/>
	<span class="disabled">NOTE: in the example below, mambo is installed in the virtual directory mambo</span>
	<p>Using the Internet Services Manager, right-click the directory in which mambo is installed.<br />
	Select properties >> Custom Error<br />
	set the 404 to URL:/mambo/index.php<br />
	set the 405 to URL:/mambo/index.php</p></li>
</ol>

<h4>Uninstall</h4>
<ol>
 <li>Uninstall the component using the component unistaller in the usual way.</li>
 <li>For apache, remove the following lines in your &quot;.htaccess&quot; files :<br />
	<code>
##<br />
# @version $Id: /depot/xhub/trunk/cms/components/com_sef/readme.inc 407 2007-09-18T15:22:32.301490Z nkissebe  $<br />
# @package Mambo_4.5<br />
# @copyright (C) 2000 - 2004 Miro International Pty Ltd<br />
# @license http://www.gnu.org/copyleft/gpl.html GNU/GPL<br />
# Mambo is Free Software<br />
##<br />
<br />
#<br />
#  mod_rewrite in use<br />
#<br />
<br />
RewriteEngine On<br />
<br />
#  for apache on windows you might need to uncomment<br />
#  this Options line<br />
#Options +SymlinksIfOwnerMatch<br />
<br />
#  Uncomment following line if your webserver's URL <br />
#  is not directly related to physival file paths.<br />
#  Update YourMamboDirectory (just / for root)<br />
<br />
#RewriteBase /<br />
#<br />
#  Rules<br />
#<br />
<br />
RewriteCond %{REQUEST_FILENAME} !-f<br />
RewriteCond %{REQUEST_FILENAME} !-d<br />
RewriteRule ^(.*) /index.php<br />
	</code></li>
 <li>For IIS, remove C:\Inetpub\wwwroot\web.config and<br/>
	the Custom Errors you created with the Internet Services Manager</li>
</ol>
	
<script type="text/javascript">
if(document.getElementById('collapsibleList')) {
 document.getElementById('collapsibleList').style.listStyle="none"; // remove list markers
 document.getElementById('install').style.display="none"; // collapse list
 document.getElementById('iis').style.display="none"; // collapse list
 document.getElementById('uninstall').style.display="none"; // collapse list
}
 // this function toggles the status of a list
 function toggle(image,list){
 var listElementStyle=document.getElementById(list).style;
 if (listElementStyle.display=="none"){
 	listElementStyle.display="block"; document.getElementById(image).src="components/com_sef/images/down.png";
 	document.getElementById(image).alt="Close list";
 }else{
 	listElementStyle.display="none";
 	document.getElementById(image).src="components/com_sef/images/up.png";
 	document.getElementById(image).alt="Open list";
 }
}
</script>

<p>Copyright &copy; 2004 W.H.Welch<br/>
Distributed under the terms of the GNU General Public License<br/>
This software may be used without warranty provided and these statements are left intact.</p>