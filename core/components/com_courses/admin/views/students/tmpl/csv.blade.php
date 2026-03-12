{{--
  Courses: Students — CSV export (outputs raw CSV, no layout)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $encodeCSVField = function ($string) {
      if (strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
          $string = '"' . str_replace('"', '""', $string) . '"';
      }
      return $string;
  };

  header('Content-type: text/csv');
  header('Content-Disposition: attachment; filename=registrations.csv');
  header('Pragma: no-cache');
  header('Expires: 0');

  foreach ($rows as $row) {
      $section = \Components\Courses\Models\Section::getInstance($row->get('section_id'));

      echo $encodeCSVField($row->get('user_id'));
      echo ',';
      echo $encodeCSVField($row->get('name'));
      echo ',';
      echo $encodeCSVField($row->get('email'));
      echo ',';
      echo $section->exists()
          ? $encodeCSVField($section->get('title'))
          : $encodeCSVField(Lang::txt('COM_COURSES_NONE'));
      echo ',';
      if ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00') {
          echo $encodeCSVField(Date::of($row->get('enrolled'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')));
      } else {
          echo $encodeCSVField(Lang::txt('COM_COURSES_UNKNOWN'));
      }
      echo "\n";
  }

  die;
@endphp
