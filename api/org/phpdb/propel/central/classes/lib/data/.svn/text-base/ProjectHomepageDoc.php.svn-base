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
class ProjectHomepageDoc extends ProjectHomepage {

	/**
	 * Constructs a new ProjectHomepageDoc class, setting the PROJECT_HOMEPAGE_TYPE_ID column to ProjectHomepagePeer::CLASSKEY_4.
	 */
	public function __construct()
	{

		$this->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_4);
	}


	public function getDocDivTag() {

      $doc_df = $this->getDataFile();
      $url = $doc_df->get_url();
      $doc_name = $doc_df->getName();

      $ico = $this->getIcon();

      $caption = trim($this->getCaption());
      $description = trim($this->getDescription());

      if(empty($caption)) $caption = $doc_name;

      $desc = empty($description) ? "" : "<br/><br/><div style='padding-left:30px;'><i>" . str_replace("\n", "<br/>", $description) . "</i></div>";

      $ret = <<<ENDHTML

              <div style="padding:20px 0;  border-bottom: 1px dashed #CCCCCC;">
                <table>
                  <tr>
                    <td><a href="$url" target="_blank"><img src="$ico" width="40" height="40" title="$doc_name" alt="" /></a></td>
                    <td>
                      <ul>
                      <li><a class="bluelt" href="$url" target="_blank">$caption</a>
                      $desc
                      </li>
                      </ul>
                    </td>
                  </tr>
                </table>
              </div>

ENDHTML;

      return $ret;
	}


	public function getIcon() {
    $doc = $this->getDataFile();

    $doc_name = $doc->getName();

	  $pathinfo = pathinfo ($doc_name);
    $extension = isset($pathinfo['extension']) ? strtolower( $pathinfo['extension'] ) : "default";

    return "/images/icons/40x40_" . $extension . ".gif";
	}

} // ProjectHomepageDoc
?>