{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$html = '';

if ($contributors) {
    $names    = [];
    $orgs     = [];
    $i        = 1;
    $k        = 0;
    $orgsln   = '';
    $names_s  = [];
    $orgsln_s = '';

    foreach ($contributors as $contributor) {
        if ($incSubmitter == false && $contributor->role == 'submitter') {
            continue;
        }

        // Build the user's name
        $name = $contributor->name
            ? e(stripslashes($contributor->name))
            : e(stripslashes($contributor->p_name));

        if ($format) {
            $nameParts = explode(' ', $name);
            $name = end($nameParts);
            $name .= count($nameParts) > 1
                ? ', ' . strtoupper(substr($nameParts[0], 0, 1)) . '.'
                : '';
            $name .= count($nameParts) > 2
                ? ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.'
                : '';
        }

        if (!$contributor->organization) {
            $contributor->organization = $contributor->p_organization;
        }
        if ($contributor->organization) {
            $contributor->organization = e(stripslashes(trim($contributor->organization)));
        }

        $name = str_replace('"', '&quot;', $name);

        // Build profile link
        if ($contributor->user_id && $contributor->open) {
            $profileUrl = Route::url(
                'index.php?option=com_members&amp;id=' . $contributor->user_id
            );
            $link = '<a href="' . $profileUrl
                . '" title="View the profile of ' . $name . '">'
                . $name . '</a>';
        } else {
            $link = $name;
        }

        $link .= ($contributor->role) ? ' (' . $contributor->role . ')' : '';

        // Track organizations with footnote numbers
        if ($contributor->organization && !in_array(trim($contributor->organization), $orgs)) {
            $orgs[$i - 1] = $contributor->organization;
            $orgsln   .= $i . '. ' . $contributor->organization . ' ';
            $orgsln_s .= $contributor->organization . ' ';
            $k = $i;
            $i++;
        } elseif ($contributor->organization) {
            $k = array_search($contributor->organization, $orgs) + 1;
        } else {
            $k = 0;
        }

        $link_s = $link;
        if ($showorgs && $k) {
            $link .= '<sup>' . $k . '</sup>';
        }

        // ORCID link
        if ($contributor->orcid) {
            $orcidUrl = 'https://orcid.org/' . $contributor->orcid;
            $orcidImg = 'https://info.orcid.org/wp-content/uploads/2019/11/orcid_16x16.png';
            $orcid = '<a href="' . $orcidUrl . '" target="blank"'
                . ' title="' . $name . '\'s ORCID page">'
                . '<img alt="ORCID logo" src="' . $orcidImg . '"'
                . ' width="16" height="16" /></a>';
            $link_s .= $orcid;
            $link .= $orcid;
        }

        $names_s[] = $link_s;
        $names[] = $link;
    }

    if (count($names) > 0) {
        if ($showaslist) {
            $html = count($names) > 1
                ? implode(', ', $names)
                : implode(', ', $names_s);
        } else {
            $html = '<p>' . ucfirst(Lang::txt('By')) . ' ';
            $html .= count($names) > 1 && count($orgs) > 0
                ? implode(', ', $names)
                : implode(', ', $names_s);
            $html .= '</p>';

            if ($showorgs && count($orgs) > 0) {
                $html .= '<p class="orgs">';
                $html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
                $html .= '</p>';
            }
        }
    }
}
@endphp

{!! $html !!}
