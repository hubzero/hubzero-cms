{{--
  Resource submitter display — shows submitters with optional role badges.

  Variables:
    $option       — component option string
    $contributors — collection of contributor objects
    $badges       — bool, whether to show role badges
    $showorgs     — bool, whether to show organizations

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $html = '';

  if ($contributors) {
      $names = [];
      $orgs = [];
      $i = 1;
      $k = 0;
      $orgsln = '';
      $names_s = [];
      $orgsln_s = '';

      $database = App::get('db');

      $types = [
          'manager'       => 'manager',
          'administrator' => 'administrator',
          'super users'   => 'super administrator',
          'publisher'     => 'publisher',
          'editor'        => 'editor',
      ];

      foreach ($contributors as $contributor) {
          if (strtolower($contributor->role) != 'submitter') {
              continue;
          }

          if ($contributor->name) {
              $name = $contributor->name;
          } elseif ($contributor->surname || $contributor->givenName) {
              $name = stripslashes($contributor->givenName) . ' ';
              if ($contributor->middleName != null) {
                  $name .= stripslashes($contributor->middleName) . ' ';
              }
              $name .= stripslashes($contributor->surname);
          } else {
              $name = $contributor->xname;
          }

          $name = e(stripslashes($name));

          $link = $name;
          if ($contributor->authorid) {
              $profile = User::getInstance($contributor->authorid);
              if ($profile->get('id') && in_array($profile->get('access'), User::getAuthorisedViewLevels())) {
                  $link = '<a href="' . Route::url($profile->link()) . '" rel="contributor" title="'
                      . Lang::txt('COM_RESOURCES_VIEW_MEMBER_PROFILE', $name) . '">' . $name . '</a>';
              }
          }

          if (!empty($badges)) {
              $xuser = User::getInstance($contributor->id);
              if (is_object($xuser) && $xuser->get('name')) {
                  $groupIds = Hubzero\Access\Access::getGroupsByUser($xuser->id, false);
                  $database->setQuery(
                      "SELECT title FROM `#__usergroups` WHERE `id` IN ("
                      . implode(',', $groupIds) . ") ORDER BY lft ASC"
                  );
                  $groups = array_reverse($database->loadColumn());
                  $gid = isset($groups[0]) ? strtolower($groups[0]) : null;

                  if (isset($types[$gid])) {
                      $link .= ' <span class="badge badge-sm badge-ghost">'
                          . str_replace(' ', '-', $types[$gid]) . '</span>';
                  }
              }
          }

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
          $html  = '<p>';
          $html .= count($orgs) > 1 ? implode(', ', $names) : implode(', ', $names_s);
          $html .= '</p>';
      }
      if (!empty($showorgs) && count($orgs) > 0) {
          $html .= '<p class="text-sm text-base-content/60">';
          $html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
          $html .= '</p>';
      }
  }
@endphp

{!! $html !!}
