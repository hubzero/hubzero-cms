<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!isset($this->link))
{
	$this->link = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->question->link()), '/');
}

$message  = Lang::txt('COM_ANSWERS_EMAIL_AUTO_GENERATED') . "\n";
$message .= '----------------------------' . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_QUESTION')) . ' #' . $this->question->get('id') . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_SUMMARY')) . ': ' . $this->question->get('subject') . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_CREATED')) . ': ' . $this->question->get('created') ."\n";
$message .= '----------------------------' . "\n\n";
$message .= 'A response has been posted to Question #' . $this->question->get('id') . ' by: ';
$message .= ($this->row->get('anonymous')) ? 'Anonymous' . "\n" : $this->row->creator->get('name') . "\n";
$message .= 'Response created: ' . $this->row->get('created') . "\n";
$message .= 'Response: ' . "\n\n";
$message .= '"' . $this->row->content . '"' . "\n\n";
$message .= 'To view the full question and responses, go to: ' . "\n";
$message .= $this->link . "\n";

echo $message;