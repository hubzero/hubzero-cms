{{--
  Screenshots partial — displays tool screenshot gallery.

  Variables (from parent view):
    $id        — int: resource ID
    $created   — string: resource creation date
    $upath     — string: upload path
    $versionid — int: tool version ID
    $sinfo     — collection: screenshot info records
    $slidebar  — bool: whether to use slidebar mode

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;

  $upath = $upath ?? '';
  $sinfo = $sinfo ?? [];
  $versionid = $versionid ?? 0;
  $path = \Components\Resources\Helpers\Html::buildPath($created, $id, '');
  $url = \Components\Resources\Helpers\Html::buildUrl($id, '');

  $tconfig = Component::params('com_tools');
  $allowversions = $tconfig->get('screenshot_edit');

  if ($versionid && $allowversions) {
      $url .= DS . $versionid;
      $path .= DS . $versionid;
  }

  $d = '';
  if (is_dir(PATH_APP . $upath . $path)) {
      $d = dir(PATH_APP . $upath . $path);
  }

  $images = [];
  $tns = [];
  $all = [];
  $ordering = [];

  if ($d) {
      while (false !== ($entry = $d->read())) {
          $img_file = $entry;
          if (
              is_file(PATH_APP . $upath . $path . DS . $img_file)
              && substr($entry, 0, 1) != '.'
              && strtolower($entry) !== 'index.html'
          ) {
              if (preg_match("#bmp|gif|jpg|png|swf|mov#i", $img_file)) {
                  $images[] = $img_file;
              }
              if (preg_match("/-tn/i", $img_file)) {
                  $tns[] = $img_file;
              }
              $images = array_diff($images, $tns);
          }
      }
      $d->close();
  }

  $b = 0;
  if ($images) {
      foreach ($images as $ima) {
          $new = [];
          $new['img'] = $ima;
          $new['type'] = explode('.', $new['img']);

          if (count($sinfo) > 0) {
              foreach ($sinfo as $si) {
                  if ($si->filename == $ima) {
                      $new['title'] = stripslashes($si->title);
                      $new['title'] = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $new['title']);
                      $new['ordering'] = $si->ordering;
                  }
              }
          }

          $ordering[] = $new['ordering'] ?? $b;
          $b++;
          $all[] = $new;
      }
  }

  if (count($sinfo) > 0) {
      array_multisort($ordering, $all);
  } else {
      sort($all);
  }
  $images = $all;

  $els = '';
  $k = 0;
  $g = 0;
  for ($i = 0, $n = count($images); $i < $n; $i++) {
      $tn = \Components\Resources\Helpers\Html::thumbnail($images[$i]['img']);
      $els .= ($slidebar && $i == 0) ? '<div class="showcase-pane">' . "\n" : '';

      if (is_file(PATH_APP . $upath . $path . DS . $tn)) {
          if (strtolower(end($images[$i]['type'])) == 'swf' || strtolower(end($images[$i]['type'])) == 'mov') {
              $g++;
              $title = (isset($images[$i]['title']) && $images[$i]['title'] != '')
                  ? $images[$i]['title']
                  : Lang::txt('DEMO') . ' #' . $g;
              $els .= $slidebar ? '' : '<li>';
              $els .= ' <a class="popup" href="/resources' . $url . DS . $images[$i]['img']
                  . '" title="' . $title . '">';
              $els .= '<img src="/resources' . $url . DS . $tn . '" alt="' . $title
                  . '" class="thumbima" /></a>';
              $els .= $slidebar ? '' : '</li>' . "\n";
          } else {
              $k++;
              $title = (isset($images[$i]['title']) && $images[$i]['title'] != '')
                  ? $images[$i]['title']
                  : Lang::txt('SCREENSHOT') . ' #' . $k;
              $els .= $slidebar ? '' : '<li>';
              $els .= ' <a rel="lightbox" href="/resources' . $url . DS . $images[$i]['img']
                  . '" title="' . $title . '">';
              $els .= '<img src="/resources' . $url . DS . $tn . '" alt="' . $title
                  . '" class="thumbima" /></a>';
              $els .= $slidebar ? '' : '</li>' . "\n";
          }
      }
      $els .= ($slidebar && $i == ($n - 1)) ? '</div>' . "\n" : '';
  }
@endphp

@if($els)
  <div class="sscontainer">
    @if($slidebar)
      <div id="showcase">
        <div id="showcase-prev"></div>
        <div id="showcase-window">
          <ul class="screenshots">
    @endif

    {!! $els !!}

    @if($slidebar)
          </ul>
        </div>
        <div id="showcase-next"></div>
      </div>
    @endif
  </div>
@endif
