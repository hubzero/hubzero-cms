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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for sending notifications to Slack about support tickets
 */
class plgSupportSlack extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Check if the plugin was properly configured
	 *
	 * @return  bool
	 */
	protected function isReady()
	{
		$endpoint = $this->params->get('endpoint');
		$username = $this->params->get('username');
		$channel  = $this->params->get('channel');

		if (!$endpoint || !$username || !$channel)
		{
			return false;
		}

		return true;
	}

	/**
	 * Send notification
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	protected function send($channel, $data)
	{
		if (!$channel || empty($data))
		{
			return false;
		}

		// @TODO  Move to Composer so other extensions can use it
		include_once __DIR__ . DS . 'lib' . DS . 'Client.php';
		include_once __DIR__ . DS . 'lib' . DS . 'Message.php';
		include_once __DIR__ . DS . 'lib' . DS . 'Attachment.php';
		include_once __DIR__ . DS . 'lib' . DS . 'AttachmentField.php';

		// Set up the client
		$client = new Maknz\Slack\Client(
			$this->params->get('endpoint'),
			array(
				'username'       => $this->params->get('username'),
				'channel'        => '#' . trim($channel, '#'),
				'link_names'     => ($this->params->get('link_names', 1) ? true : false),
				'allow_markdown' => ($this->params->get('allow_markdown', 1) ? true : false)
			)
		);

		try
		{
			$client->attach($data)->send();
		}
		catch (Exception $e)
		{
			// Fail silently
			return false;
		}

		return true;
	}

	/**
	 * Called after creating a ticket
	 *
	 * @param   object  $ticket
	 * @return  void
	 */
	public function onTicketSubmission($ticket)
	{
		if (!$this->isReady() || !$this->params->get('notify_created'))
		{
			return;
		}

		if ($group = $this->params->get('group_created'))
		{
			if ($group != $ticket->get('group'))
			{
				return;
			}
		}

		$channel = $this->params->get('channel');
		$url     = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($ticket->link()), '/');
		$pretext = Lang::txt('PLG_SUPPORT_SLACK_TICKET_CREATED', Config::get('sitename'), $ticket->get('name', $ticket->get('email')));
		$text    = Hubzero\Utility\String::truncate(Hubzero\Utility\Sanitize::stripWhitespace($ticket->get('report')), 300);

		if (Component::params('com_support')->get('email_terse'))
		{
			$text = Lang::txt('PLG_SUPPORT_SLACK_TICKET_NEW');
		}

		// Get the color
		$color = '#999999';
		if ($ticket->get('severity') == 'major')
		{
			$color = 'warning';
		}
		if ($ticket->get('severity') == 'critical')
		{
			$color = 'danger';
		}
		if ($ticket->get('severity') == 'minor')
		{
			$color = '#5fd2db';
		}

		$data = array(
			'fallback'   => $pretext . ': ' . $url . ' - ' . $text, // Fallback text for plaintext clients, like IRC
			'pretext'    => $pretext, // Optional text to appear above the attachment and below the actual message
			'title'      => Lang::txt('PLG_SUPPORT_SLACK_TICKET_NUMBER', $ticket->get('id')),
			'title_link' => $url,
			'text'       => $text, // The text for inside the attachment
			'color'      => $color, // Change the color of the attachment, default is 'good'. May be a hex value or 'good', 'warning', or 'danger'
		);

		$this->send($channel, $data);
	}

	/**
	 * Called after updating a ticket
	 *
	 * @param   object  $ticket
	 * @param   object  $comment
	 * @return  void
	 */
	public function onTicketUpdate($ticket, $comment)
	{
		if (!$this->isReady() || !$this->params->get('notify_updated'))
		{
			return;
		}

		if (!$this->params->get('notify_private') && $comment->isPrivate())
		{
			return;
		}

		if ($group = $this->params->get('group_updated'))
		{
			if ($group != $ticket->get('group'))
			{
				return;
			}
		}

		$channel = $this->params->get('channel_updated', $this->params->get('channel'));
		$url     = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($ticket->link()), '/');
		$pretext = Lang::txt('PLG_SUPPORT_SLACK_TICKET_UPDATED', Config::get('sitename'), $comment->creator()->get('name'));
		$text    = Hubzero\Utility\String::truncate(Hubzero\Utility\Sanitize::stripWhitespace($comment->get('comment')), 300);

		$color = 'good';
		if ($comment->isPrivate())
		{
			$color = '#ecada2';
			$pretext .= ' ' . Lang::txt('PLG_SUPPORT_SLACK_PRIVATE');
		}

		$title = Lang::txt('PLG_SUPPORT_SLACK_TICKET_NUMBER', $ticket->get('id'));

		if (Component::params('com_support')->get('email_terse'))
		{
			$text = Lang::txt('PLG_SUPPORT_SLACK_COMMENT_NEW');
		}
		else
		{
			$title .= ': ' . $ticket->get('summary');
		}

		$data = array(
			'fallback'   => $pretext . ': ' . $url . ' - ' . $text, // Fallback text for plaintext clients, like IRC
			'pretext'    => $pretext, // Optional text to appear above the attachment and below the actual message
			'title'      => $title,
			'title_link' => $url,
			'text'       => $text, // The text for inside the attachment
			'color'      => $color, // Change the color of the attachment, default is 'good'. May be a hex value or 'good', 'warning', or 'danger'
		);

		$this->send($channel, $data);
	}
}