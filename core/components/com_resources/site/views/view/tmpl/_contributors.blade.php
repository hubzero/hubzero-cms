{{--
  Resource contributor list — author names with profile links and org superscripts.

  Variables:
    $option       — component option string
    $contributors — collection of contributor objects

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $html = '';
  $names = [];
  $orgs = [];
  $i = 1;
  $k = 0;
  $orgsln = '';
  $names_s = [];
  $orgsln_s = '';

  if ($contributors) {
      foreach ($contributors as $contributor) {
          if (strtolower($contributor->role == null ? '' : $contributor->role) == 'submitter') {
              continue;
          }

          if ($contributor->name) {
              $name = e(stripslashes($contributor->name));
          } elseif ($contributor->surname || $contributor->givenName) {
              $name = e(stripslashes($contributor->givenName)) . ' ';
              if ($contributor->middleName != null) {
                  $name .= e(stripslashes($contributor->middleName)) . ' ';
              }
              $name .= e(stripslashes($contributor->surname));
          } else {
              $name = e(stripslashes($contributor->xname == null ? '' : $contributor->xname));
          }
          if (!trim($name)) {
              $name = Lang::txt('(unknown)');
          }

          $contributor->organization = e(stripslashes(trim(
              $contributor->organization ?: ''
          )));

          if (!isset($contributor->authorid) && isset($contributor->uid)) {
              $contributor->authorid = $contributor->uid;
          }

          $link = $name;
          if ($contributor->authorid) {
              $profile = User::getInstance($contributor->authorid);
              if ($profile->get('id') && in_array($profile->get('access'), User::getAuthorisedViewLevels())) {
                  $link = '<a href="' . Route::url($profile->link()) . '" rel="contributor" title="'
                      . Lang::txt('COM_RESOURCES_VIEW_MEMBER_PROFILE', $name) . '">' . $name . '</a>';
              }
          }

          $link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

          $org = trim($contributor->organization ?? '');
          if ($org != '' && !in_array($org, $orgs)) {
              $orgs[$i - 1] = $org;
              $orgsln   .= $i . '. ' . $org . ' ';
              $orgsln_s .= $org . ' ';
              $k = $i;
              $i++;
          } else {
              $k = array_search($org, $orgs) + 1;
          }
          $link_s = $link;
          if ($org != '') {
              $link .= '<sup>' . $k . '</sup>';
          }

          $names_s[] = $link_s;
          $names[] = $link;
      }

      if (count($names) > 0) {
          $html = '<p>' . Lang::txt(
              'COM_RESOURCES_BY_AUTHORS',
              (count($contributors) > 1 ? implode('; ', $names) : implode('; ', $names_s))
          ) . '</p>';
      }

      if (count($orgs) > 0) {
          $html .= '<p class="text-sm text-base-content/60">';
          $html .= count($contributors) > 1 ? $orgsln : $orgsln_s;
          $html .= '</p>';
      }
  }
@endphp

{!! $html !!}
