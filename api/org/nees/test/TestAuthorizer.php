<?php

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once 'lib/data/Project.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/security/Authorizer.php';
require_once 'lib/security/UserManager.php';

$oAuthorizer = Authorizer::getInstance();
$oAuthorizer->setUser("sjbrandenberg");

$oUserManager = UserManager::getInstance();
$oUserManager->setUser("sjbrandenberg");

$oProject = ProjectPeer::retrieveByPK(644);
if( $oAuthorizer->canView($oProject) ){
  print "View=Yes\n";
}else{
  print "View=No\n";
}

if( $oAuthorizer->canEdit($oProject) ){
  print "Edit=Yes\n";
}else{
  print "Edit=No\n";
}

?>
