/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/* Blade admin edit view for com_events.
 *
 * The legacy events.js contains jQuery UI datepicker and fancybox calls
 * that are not needed in Blade mode — flatpickr handles dates via
 * Html::input('calendar') and the recurrence UI uses a simple text field.
 *
 * This file is intentionally minimal: the admin template provides
 * Hubzero.submitbutton globally, and form validation is handled by
 * the data-attribute pattern in admin.js.
 */
