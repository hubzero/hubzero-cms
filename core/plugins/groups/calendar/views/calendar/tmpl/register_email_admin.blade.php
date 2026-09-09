{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$message  = 'Group "'
    . $group->get('description')
    . '" event registration: "'
    . $event->title
    . '"'
    . "\n\n";
$message .= 'http://'
    . $_SERVER['HTTP_HOST']
    . '/'
    . 'groups'
    . '/'
    . $group->get('cn')
    . '/'
    . 'calendar'
    . '/'
    . 'details'
    . '/'
    . $event->id;
$message .= "\n\n" . '-------------------------------------------------------------------' . "\n\n";

$message .= 'Name: ' . $register['first_name'] . ' ' . $register['last_name'] . "\n";
if ($params->get('show_title', 1) == 1 && isset($register['title'])) {
    $message .= 'Title: ' . $register['title'] . "\n";
}
if ($params->get('show_affiliation', 1) == 1 && isset($register['affiliation'])) {
    $message .= 'Affiliation: ' . $register['affiliation'] . "\n";
}
if ($params->get('show_email', 1) == 1 && isset($register['email'])) {
    $message .= 'Email: ' . $register['email'] . "\n";
}
if ($params->get('show_website', 1) == 1 && isset($register['website'])) {
    $message .= 'Website: ' . $register['website'] . "\n";
}
if ($params->get('show_telephone', 1) == 1 && isset($register['telephone'])) {
    $message .= 'Telephone: ' . $register['telephone'] . "\n";
}
if ($params->get('show_fax', 1) == 1 && isset($register['fax'])) {
    $message .= 'Fax: ' . $register['fax'] . "\n\n";
}
if ($params->get('show_address', 1) == 1) {
    if (isset($register['city'])) {
        $message .= 'City: ' . $register['city'] . "\n";
    }
    if (isset($register['state'])) {
        $message .= 'State/Province: ' . $register['state'] . "\n";
    }
    if (isset($register['zip'])) {
        $message .= 'Zip/Postal Code: ' . $register['zip'] . "\n";
    }
    if (isset($register['country'])) {
        $message .= 'Country: ' . $register['country'] . "\n\n";
    }
}

if (
    $params->get('show_position', 1) == 1 && (isset($register['position']) ||
    isset($register['position_other']))
) {
    $message .= 'Current position: ';
    $message .= ($register['position']) ? $register['position'] : $register['position_other'];
    $message .= "\n\n";
}
if ($params->get('show_degree', 1) == 1 && isset($register['degree'])) {
    $message .= 'Highest degree earned: ' . $register['degree'] . "\n\n";
}
if ($params->get('show_gender', 1) == 1 && isset($register['sex'])) {
    $message .= 'Gender: ' . $register['sex'] . "\n\n";
}
if ($params->get('show_race', 1) == 1 && isset($race)) {
    $tribe = '';
    if (isset($race['nativetribe'])) {
        $tribe = $race['nativetribe'];
        unset($race['nativetribe']);
    }
    $message .= 'Race: ' . implode(', ', $race);
    $message .= ($tribe != '') ? ', ' . $tribe : '';
    $message .= "\n\n";
}

if ($params->get('show_disability', 1) == 1) {
    if ($disability) {
        $message .= '[X] I have auxiliary aids or services due to a disability. Please contact me.' . "\n\n";
    } else {
        $message .= '[ ] I have auxiliary aids or services due to a disability. Please contact me.' . "\n\n";
    }
}
if ($params->get('show_dietary', 1) == 1) {
    if (isset($dietary['needs']) || (isset($dietary['specific']) && $dietary['specific'] != '')) {
        $message .= '[X] I have specific dietary needs.' . "\n\n";
        $message .= '    Specific: ' . $dietary['specific'] . "\n\n";
    } else {
        $message .= '[ ] I have specific dietary needs.' . "\n\n";
    }
}

if ($params->get('show_arrival', 1) == 1 && $arrival) {
    $message .= '=== Arrival ===' . "\n";
    $message .= 'Arrival Day: ' . $arrival['day'] . "\n";
    $message .= 'Arrival Time: ' . $arrival['time'] . "\n\n";
}
if ($params->get('show_departure', 1) == 1 && $departure) {
    $message .= '=== Departure ===' . "\n";
    $message .= 'Departure Day: ' . $departure['day'] . "\n";
    $message .= 'Departure Time: ' . $departure['time'] . "\n\n";
}

if ($params->get('show_dinner', 1) == 1) {
    if ($dinner) {
        $message .= '[x] Attending dinner.' . "\n\n";
    } else {
        $message .= '[ ] Attending dinner.' . "\n\n";
    }
}

if ($params->get('show_abstract', 1) == 1 && isset($register['abstract'])) {
    $message .= 'Abstract: ' . $register['abstract'] . "\n\n";
}
if ($params->get('show_comments', 1) == 1 && isset($register['comment'])) {
    $message .= 'Comments: ' . $register['comment'] . "\n\n";
}
@endphp
{{ $message }}
