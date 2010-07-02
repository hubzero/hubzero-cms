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
class ProjectHomepageURL extends ProjectHomepage {

	/**
	 * Constructs a new ProjectHomepageURL class, setting the PROJECT_HOMEPAGE_TYPE_ID column to ProjectHomepagePeer::CLASSKEY_1.
	 */
	public function __construct()
	{

		$this->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_1);
	}


	public function getUrlDivTag() {

    $url = $this->getUrl();
    $caption = trim($this->getCaption());
    if(empty($caption)) $caption = $url;

    $description = trim($this->getDescription());
    $desc = empty($description) ? "" : "<br/><br/><div style='padding-left:30px;'><i>" . str_replace("\n", "<br/>", $description) . "</i></div>";
    $server = $_SERVER['SERVER_NAME'];
    $target = strpos($url, $server) || strpos($url, "/") === 0 ? "" : "target='blank'";

    $ret = <<<ENDHTML

              <div style="padding:20px 0;  border-bottom: 1px dashed #CCCCCC;">
                <table>
                  <tr>
                    <td><a href="$url" $target><img src="/images/icons/40x40_url.gif" width="40" height="40" title="$caption" alt="" /></a></td>
                    <td>
                      <ul>
                      <li><a class="bluelt" href="$url" $target>$caption</a>
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
} // ProjectHomepageURL
?>
