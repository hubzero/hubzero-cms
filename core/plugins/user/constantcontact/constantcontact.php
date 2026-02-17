<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Constantcontact;

use Hubzero\Plugin\Plugin;

/**
 * User plugin for syncing email preferences with Constant Contact (V3 API)
 */
class Constantcontact extends Plugin
{
    /**
     * Build the API client from plugin params
     *
     * @return Helpers\ConstantContactClient|null  Client or null if unconfigured
     */
    protected function getClient(): ?Helpers\ConstantContactClient
    {
        $clientId     = $this->params->get('ccClientId', '');
        $clientSecret = $this->params->get('ccClientSecret', '');
        $accessToken  = $this->params->get('ccAccessToken', '');
        $refreshToken = $this->params->get('ccRefreshToken', '');
        $tokenExpires = (int) $this->params->get('ccTokenExpires', 0);

        if (!$clientId || !$clientSecret || !$refreshToken) {
            return null;
        }

        require_once __DIR__ . '/helpers/ConstantContactClient.php';

        return new Helpers\ConstantContactClient(
            $clientId,
            $clientSecret,
            $accessToken,
            $refreshToken,
            $tokenExpires,
            [$this, 'persistTokens']
        );
    }

    /**
     * Persist refreshed OAuth2 tokens back to the database
     *
     * Called automatically by the client when tokens are refreshed.
     *
     * @param  string $accessToken
     * @param  string $refreshToken
     * @param  int    $tokenExpires
     * @return void
     */
    public function persistTokens(string $accessToken, string $refreshToken, int $tokenExpires): void
    {
        $this->params->set('ccAccessToken', $accessToken);
        $this->params->set('ccRefreshToken', $refreshToken);
        $this->params->set('ccTokenExpires', $tokenExpires);

        try {
            $db = \App::get('db');
            $db->setQuery(
                "UPDATE `#__extensions` SET `params` = " . $db->quote($this->params->toString())
                . " WHERE `type` = 'plugin' AND `folder` = 'user' AND `element` = 'constantcontact'"
            );
            $db->query();
        } catch (\Exception $e) {
            \Log::error('Constant Contact: failed to persist refreshed tokens: ' . $e->getMessage());
        }
    }

    /**
     * Determine the contact list ID to use
     *
     * Uses the configured list ID if set, otherwise falls back to the
     * first list returned by the API (matching V1 behavior).
     *
     * @param  Helpers\ConstantContactClient $client
     * @return string|null
     */
    protected function getDefaultListId(Helpers\ConstantContactClient $client): ?string
    {
        $listId = $this->params->get('ccListId', '');
        if ($listId) {
            return $listId;
        }

        $lists = $client->getLists();

        return !empty($lists) ? $lists[0]['list_id'] : null;
    }

    /**
     * Method is called after user data is stored in the database
     *
     * Syncs the user's email preference with Constant Contact:
     * - Creates a contact if none exists
     * - Re-subscribes if user opts in (sendEmail == 2) and contact is unsubscribed
     * - Opts out if user opts out (sendEmail == 0) and contact is active
     *
     * @param  object $user  The user profile (\Hubzero\User\User)
     * @return void
     */
    public function onAfterStoreProfile($user)
    {
        if (!$this->params->get('ccManageEmailPreference', 0)) {
            return;
        }

        $email     = $user->get('email');
        $sendEmail = $user->get('sendEmail');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $client = $this->getClient();
        if (!$client) {
            return;
        }

        try {
            $listId = $this->getDefaultListId($client);
            if (!$listId) {
                return;
            }

            $contact = $client->searchContactByEmail($email);

            if (!$contact) {
                $client->createContact($email, $listId);
                return;
            }

            $contactId        = $contact['contact_id'];
            $permissionToSend = $contact['email_address']['permission_to_send'] ?? '';

            // Unsubscribed + user opts in → re-subscribe
            if ($permissionToSend === 'unsubscribed' && $sendEmail == 2) {
                $client->updateContact($contactId, [
                    'email_address' => [
                        'address'            => $email,
                        'permission_to_send' => 'implicit',
                    ],
                    'list_memberships' => [$listId],
                    'update_source'    => 'Contact',
                ]);
            }
            // Active + user opts out → unsubscribe
            elseif (in_array($permissionToSend, ['implicit', 'explicit']) && $sendEmail == 0) {
                $client->deleteContact($contactId);
            }
        } catch (\RuntimeException $e) {
            \Log::error('Constant Contact: onAfterStoreProfile: ' . $e->getMessage());
        }
    }

    /**
     * Method is called after user data is deleted from the database
     *
     * Removes the contact from Constant Contact (opts them out).
     *
     * @param  object $user  The user profile (\Hubzero\User\User)
     * @return void
     */
    public function onAfterDeleteProfile($user)
    {
        $email = $user->get('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $client = $this->getClient();
        if (!$client) {
            return;
        }

        try {
            $contact = $client->searchContactByEmail($email);

            if ($contact) {
                $client->deleteContact($contact['contact_id']);
            }
        } catch (\RuntimeException $e) {
            \Log::error('Constant Contact: onAfterDeleteProfile: ' . $e->getMessage());
        }
    }
}
