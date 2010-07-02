<?php
//error_reporting(E_ALL & ~E_NOTICE);


require_once "lib/data/tsunami/util/TsunamiBase.php";
require_once "lib/data/tsunami/TsunamiDocLib.php";

function processTsunamiFile($filename,$filepath)
{
  include ("domain/tsunami/util/Catalog.php");
  //		$fp=fopen("processTsunamiFile.log","w");

  //		fprintf($fp,"REQ: %s\n",print_r($_REQUEST,true));
  //		fflush($fp);



  $mData = array();
  foreach ($subCategory as $subcat => $sc) {
    if (isset($_REQUEST[$sc[1]])){ // found a Metadata id
      //				fprintf($fp,"Found SubCat: %s\n",$sc[1]);
      //				fflush($fp);
      $topic = $Category[$_REQUEST[$sc[1]]][1]; //Metadata topic
      if (!isset($mData[$topic])){ //first time
        $mData[$topic]= TsunamiPropel::newTsunamiObject($topic);
      }
      $setCmd = "set".ucfirst($sc[1]);
      $mData[$topic]->{$setCmd}(1);
    }
  }

  //Need some NEES clean-up
  $loc = preg_replace("/\/\.\//","/",$filepath);
  $loc = preg_replace("/^\/nees\/home/","",$loc);

  $dl = new TsunamiDocLib();
  $dl->setTsunamiProjectId($_REQUEST['pId']);
  $dl->setName($filename);
  $dl->setFileLocation($loc);
  $dl->setDirty(1);
  $dl->setTypeOfMaterial($Material[$_REQUEST['mtype']]);
  $dl->save();

  TsunamiDocLibPeer::linkDocToSite($dl,$_REQUEST['sId']);

  $dID=$dl->getId();

  foreach ($mData as $type => $obj) {
    $obj->setTsunamiDocLibId($dID);
    $obj->save();
  }


  //		fclose($fp);
}
?>
