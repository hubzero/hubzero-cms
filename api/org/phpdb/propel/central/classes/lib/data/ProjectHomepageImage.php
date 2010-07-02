<?php

require_once 'lib/data/ProjectHomepage.php';

require_once 'lib/data/om/BaseProjectHomepage.php';


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
class ProjectHomepageImage extends ProjectHomepage {

	/**
	 * Constructs a new ProjectHomepageImage class, setting the PROJECT_HOMEPAGE_TYPE_ID column to ProjectHomepagePeer::CLASSKEY_2.
	 */
	public function __construct()
	{

		$this->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_2);
	}


	/**
	 * Get the HTML code for the Div of image thumbnail
	 *
	 * @return String $html
	 */
	public function getThumbnailDivTag() {

    $img = $this->getDataFile();
    $img_name = $img->getName();

    // Should not happen, just in case
    if(!$img) return "";

    $img_url = $img->get_url();
    $caption = htmlspecialchars($this->getCaption(), ENT_QUOTES);

    $thumbId = $img->getImageThumbnailId();

    $thumb_url = null;

    if($thumbId && $thumb = DataFilePeer::find($thumbId)) {
      if (file_exists($thumb->getFullPath())) {
        $thumb_url = $thumb->get_url();
      }
    }

    //Couldn't find thumbnail. Get the default thumbnail instead
    if(!$thumb_url) {
      $thumb_url = "/images/icons/60x60_image.gif";
    }

    return "<div class='thumb_frame'><a href='$img_url' target='_blank'><img src='$thumb_url' title='$img_name \n$caption' alt='' /></a></div>";
	}

} // ProjectHomepageImage
?>