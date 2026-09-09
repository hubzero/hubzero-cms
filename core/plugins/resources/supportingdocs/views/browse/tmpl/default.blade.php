{{--
  Supporting docs — child resources list with file type icons and access control.

  Variables (from plugin):
    $option — string: component option
    $model  — object: resource model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  if ($model->isTool()) {
      $children = $model->children()
          ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
          ->order('ordering', 'asc')
          ->rows();
  } else {
      $children = $model->children()
          ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
          ->whereEquals('standalone', 0)
          ->order('ordering', 'asc')
          ->rows();
  }
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS') }}</h3>

<div id="supportingdocs" class="supportingdocs">
  @if($children)
    @php
      $linkAction = 0;
      $base = $model->params->get('uploadpath');
      $i = 0;
      $displayedDocs = [];

      $xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
      $usersgroups = [];
      if (!empty($xgroups)) {
          foreach ($xgroups as $group) {
              if ($group->regconfirmed) {
                  $usersgroups[] = $group->cn;
              }
          }
      }
      $someDocsHidden = false;

      foreach ($children as $child) {
          if (
              $child->access == 0
              || ($child->access == 1 && !User::isGuest())
              || ($child->access == 3 && in_array($model->group_owner, $usersgroups))
          ) {
              $i++;
              $ftype = Filesystem::extension($child->path);
              if (substr($child->path, 0, 4) == 'http') {
                  $ftype = 'html';
              }

              $class = '';
              $action = '';
              if ($child->standalone == 1) {
                  $liclass = ' class="html"';
                  $title = stripslashes($child->title);
              } else {
                  $rt = $child->type;
                  $tparams = $rt->params;
                  $lt = $child->logicaltype;
                  $ltparams = $lt->params;

                  if ($child->logical_type) {
                      $rtLinkAction = $ltparams->get('linkAction', 'extension');
                  } else {
                      $rtLinkAction = $tparams->get('linkAction', 'extension');
                  }

                  switch ($rtLinkAction) {
                      case 'download':
                          $class = 'download';
                          $linkAction = 3;
                          break;
                      case 'lightbox':
                          $class = 'play';
                          $linkAction = 2;
                          break;
                      case 'newwindow':
                          $action = 'rel="external"';
                          $linkAction = 1;
                          break;
                      case 'extension':
                      default:
                          $linkAction = 0;
                          $mediatypes = [
                              'elink', 'quicktime', 'presentation', 'presentation_audio',
                              'breeze', 'quiz', 'player', 'video_stream', 'video', 'hubpresenter',
                          ];
                          $downtypes = ['thesis', 'handout', 'manual', 'software_download'];
                          if (in_array($lt->alias, $downtypes)) {
                              $class = 'download';
                          } elseif (in_array($rt->alias, $mediatypes)) {
                              $mediatypes2 = ['flash_paper', 'breeze', '32', '26'];
                              if (in_array($child->get('type'), $mediatypes2)) {
                                  $class = 'play';
                              }
                          } else {
                              $class = 'download';
                          }
                          break;
                  }

                  $childParams = $child->params;
                  $linkAction = intval($childParams->get('link_action', $linkAction));
                  switch ($linkAction) {
                      case 3: $class = 'download'; break;
                      case 2: $class = 'play'; break;
                      case 1: $action = 'rel="external"'; break;
                      default: break;
                  }

                  switch ($rt->alias) {
                      case 'user_guide':    $liclass = ' class="guide"'; break;
                      case 'ilink':         $liclass = ' class="html"'; break;
                      case 'breeze':        $liclass = ' class="swf"'; break;
                      case 'hubpresenter':
                          $liclass = ' class="presentation"';
                          $class = 'hubpresenter';
                          break;
                      default:
                          $liclass = ' class="' . strtolower($ftype) . '"';
                          break;
                  }

                  $title = ($child->logical_type) ? $child->logicaltype->type : stripslashes($child->title);
              }

              $url = \Components\Resources\Helpers\Html::processPath($option, $child, $model->id, $linkAction);

              // Image dimensions
              if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $child->path)) {
                  if (!preg_match("/(?:https?:|mailto:|ftp:|gopher:|news:|file:)/", $child->path)) {
                      $filename = $child->path;
                      if (substr($filename, 0, 1) != DS) {
                          $filename = DS . $filename;
                          if (substr($filename, 0, strlen($base)) != $base) {
                              $filename = $base . $filename;
                          }
                      }
                      $filename = PATH_APP . $filename;

                      $width = 0;
                      $height = 0;
                      if (file_exists($filename)) {
                          list($width, $height) = getimagesize($filename);
                      }
                      if ($width > 0 && $height > 0) {
                          $class .= ' ' . $width . 'x' . $height;
                      }
                  }
              } else {
                  $attribs = $child->attribs;
                  $width  = intval($attribs->get('width', 640));
                  $height = intval($attribs->get('height', 360));
                  if ($width > 0 && $height > 0) {
                      $class .= ' ' . $width . 'x' . $height;
                  }
              }

              if (strtolower($title) != preg_replace('/user guide/', '', strtolower($title))) {
                  $liclass = ' class="guide"';
              }

              $classAttr = $class ? ' class="' . $class . '"' : '';
              $displayedDocs[] = '<li' . $liclass . '>'
                  . \Components\Resources\Helpers\Html::getFileAttribs($child->path, $base, 0)
                  . '<a' . $classAttr . ' href="' . $url . '" title="'
                  . e(stripslashes($child->title)) . '"' . $action . '>'
                  . $title . '</a></li>';
          } else {
              $someDocsHidden = true;
          }
      }
    @endphp

    @if($someDocsHidden && User::isGuest())
      @php
        $reqUrl = Request::getString('REQUEST_URI', '', 'server');
        $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($reqUrl));
      @endphp
      <p class="warning">{!! Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS_LOGIN_TO_SEE_MORE', $loginUrl) !!}</p>
    @endif

    @if(count($displayedDocs) > 0)
      <ul>
        @foreach($displayedDocs as $doc)
          {!! $doc !!}
        @endforeach
      </ul>
    @endif
  @else
    <p>{{ Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS_NONE') }}</p>
  @endif

  <div class="customfields">
    @php
      $data = [];
      preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $model->fulltxt, $matches, PREG_SET_ORDER);
      foreach ($matches as $match) {
          $data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
      }

      $elements = new \Components\Resources\Models\Elements($data, $model->type->customFields);
      $schema = $elements->getSchema();
      $tab = Request::getCmd('active', 'supportingdocs');

      if (is_object($schema)) {
          if (!isset($schema->fields) || !is_array($schema->fields)) {
              $schema->fields = [];
          }
          foreach ($schema->fields as $field) {
              if (isset($data[$field->name])
                  && $elements->display($field->type, $data[$field->name])
                  && isset($field->display) && $field->display == $tab
              ) {
                  echo '<h4>' . $field->label . '</h4>';
                  echo '<div class="resource-content">';
                  echo $elements->display($field->type, $data[$field->name]);
                  echo '</div>';
              }
          }
      }
    @endphp
  </div>
</div>
