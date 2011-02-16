<?php

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once 'lib/data/ResearcherKeywordPeer.php';
require_once 'lib/data/ResearcherKeyword.php';

$oConnection = Propel::getConnection();

try{
  $oConnection->begin();

  $oResearcherKeyword1 = new ResearcherKeyword("Test1", date("m/d/Y"), "gemezm", 917, 1);
  $oResearcherKeyword1->save();
  echo "1 updated\n";

  //should fail.  no attributes should be null...
  $oResearcherKeyword2 = new ResearcherKeyword("Test2", null, "gemezm", 917, 1);
  $oResearcherKeyword2->save();
  echo "2 updated\n";

  $oResearcherKeyword3 = new ResearcherKeyword("Test3", date("m/d/Y"), "gemezm", 917, 1);
  $oResearcherKeyword3->save();
  echo "3 updated\n";

  $oConnection->commit();
  echo "committed\n";
}catch(Exception $e){
  $oConnection->rollback();
  echo "rollback: ".$e->getMessage()."\n";
}

?>
