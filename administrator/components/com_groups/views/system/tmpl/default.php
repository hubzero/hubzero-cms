<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'GROUPS' ).': <small><small>[ '.JText::_('System').' ]</small></small>', 'user.png' );
JToolBarHelper::cancel();

?>

<br /> 
LDAP Connection: <?echo $this->status['ldap'];?> <br />
LDAP organizationalUnit "ou=groups" exists: <?echo $this->status['ldap_groupou'];?> <br />
LDAP objectClass "hubGroup" exists: <?echo $this->status['ldap_hubgroup'];?> <br />
LDAP objectClass "posixGroup" exists: <?echo $this->status['ldap_posixgroup'];?> <br />
<br />
<br />
<a href="index.php?option=com_groups&task=exporttoldap">Export to LDAP</a>
<br />
<br />
<a href="index.php?option=com_groups&task=importldap">Import from LDAP</a>
<br />
<br />
