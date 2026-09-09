{{--
 * Project error page
 *
 * Variables:
 *   $title - Page title
 *   $error - Error message string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="$title">
    <p class="alert alert-error">{{ $error }}</p>
</x-page-container>
