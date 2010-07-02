<?php

require_once 'lib/data/ProjectHomepage.php';

require_once 'lib/data/om/BaseProjectHomepage.php';
require_once 'lib/common/ImageThumbnail.php';

/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'PROJECT_HOMEPAGE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectHomepageVideo extends ProjectHomepage {

	/**
	 * Constructs a new ProjectHomepageVideo class, setting the PROJECT_HOMEPAGE_TYPE_ID column to ProjectHomepagePeer::CLASSKEY_3.
	 */
	public function __construct()
	{

		$this->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_3);
	}


	/**
	 * Get the HTML code for the Div of image thumbnail
	 *
	 * @return String $html
	 */
	public function getThumbnailDivTag() {

    $videodf = $this->getDataFile();

    // Should not happen, just in case
    if(!$videodf) return "";

    $video_url = $videodf->get_url();
    $video_name = $videodf->getName();

    $caption = htmlspecialchars($this->getCaption(), ENT_QUOTES);

    $thumbId = $videodf->getThumbId();

    $thumb_url = null;

    // In most cases, the ThumbId should be existed, if there a NULL thumbid
    // Second chance to make a new thumbnail for this video
    if(!$thumbId) {
      $input = $videodf->getFullPath();

      $publicDir = "/nees/home/Public.groups";
      $thumbname = time() . "_" . $videodf->getName() . ".png";

      $output = $publicDir . "/" . $thumbname;

      $thumbObj = new ImageThumbnail();

      if($thumbObj->createVideoThumbnail($input, $output)) {
        $thumb_df = DataFilePeer::insertOrUpdateIfDuplicate($thumbname, $publicDir, date('Y-m-d H:i:s'), md5_file($output), 0, filesize($output));
      }

      $thumb_url = $thumb_df ? $thumb_df->get_url()  : null;
    }
    else {
      if($thumbId && $thumb = DataFilePeer::find($thumbId)) {
        if (file_exists($thumb->getFullPath())) {
          $thumb_url = $thumb->get_url();
        }
      }
    }

    //Couldn't find thumbnail. Get the default thumbnail instead
    if(!$thumb_url) {
      $thumb_url = "/images/icons/60x60_video.gif";
    }

    return "<div class='thumbvideo_frame'><a href='$video_url' target='_blank'><img src='$thumb_url' title='$video_name \n$caption' alt='' /></a></div>";
	}

} // ProjectHomepageVideo
?>