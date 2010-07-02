<?php
################################################################################
##   /handler/file_curate.php
##   by Fazle Rabbi (Evoknow) (c) 2005
##
##   file curation form handler
################################################################################
require_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';

## set title to filename if needed
$display_title = $title . " ($qfile)";
if (strlen($title) == 0) {
  $display_title = $qfile;
}

## set NEEScentral return path
$returnPath    = "/"."$get_encoded_path&floc=Details&Q=$queryfile&isdir=$_REQUEST[isdir]";
$source        = $_REQUEST['Q'];
$curation      = $_REQUEST['curate_doc'];
$projid        = $_REQUEST['projid'];
$expid         = $_REQUEST['expid'];

## check for authentication
$authenticator = Authenticator::getInstance();

if ( $authenticator->isLoggedIn() ) {

   ## Get document metadata
   $fullPath  = FileCommandAPI::set_directory($source);
   $dataFile  = DataFilePeer::findByFullPath($fullPath);
   $tblSource = 'DataFile';

   if(!$dataFile){
      $filePath      = dirname($fullPath);
      $curationError = "Document is not available in $filePath.";
      require_once "lib/template/file_curate.php";
   }

   else {
      $docid = $dataFile->getId();

      ## Want to create trial data. Need to curate Experiment First

      if($expid && count(NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($expid, 'Experiment') <= 0)){
         $curationError = "Please curate experiment first.";
         require_once "lib/template/file_curate.php";
      }
      else{
         ## Verify whether document has been curated or not
         $crossRef  = NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($docid, $tblSource);

         ## Get curation id
         if(count($crossRef) > 0){
            $curatedId = $crossRef[0]->getCuratedEntityID();
         }

         if($curation === 'edit'){
            if($curatedId){
               $curationError = "Document already curated.";
               require_once "lib/template/file_curate.php";
            }
            else{
               $_SESSION['NEEScentralReturnPath'] = $returnPath;
               header("Location:/curation/run.php/Curation?id=$docid&projid=$projid&expid=$expid&cmd=curate&table=$tblSource");
               exit;
            }
         }

         if($curation === 'view'){
            if($curatedId === null){
               $curationError = "Item has not been curated.";
               require_once "lib/template/file_curate.php";
            }
            else{
               $_SESSION['NEEScentralReturnPath'] = $returnPath;
               header("Location:/curation/run.php/CatalogListing?cmd=curate&id=$curatedId");
               exit;
            }
         }
      }
   }
}

?>