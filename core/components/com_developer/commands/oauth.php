<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Components\Developer\Models\Application;
use Hubzero\User\User;

/**
 * Manage OAuth client applications from the command line.
 *
 * The web UI at /developer/api/applications is the only way to register an
 * OAuth client on a stock hub, which makes provisioning awkward to script or to
 * do on behalf of a service account. These tasks drive the same model, so the
 * automatic client_id/client_secret/grant_types generation and the redirect_uri
 * validation behave identically.
 */
class Oauth extends Base implements CommandInterface
{
    /**
     * Default (required) command - show help
     *
     * @return  void
     **/
    public function execute()
    {
        $this->output = $this->output->getHelpOutput();
        $this->help();
        $this->output->render();
    }

    /**
     * List registered applications
     *
     * @museDescription  Lists registered OAuth applications
     *
     * @return  void
     **/
    public function show()
    {
        $id = $this->arguments->getOpt('id');

        if ($id) {
            $this->detail($id);
            return;
        }

        $query = Application::all();

        if ($user = $this->arguments->getOpt('user')) {
            $query->whereEquals('created_by', $this->resolveUser($user)->get('id'));
        }

        if ($search = $this->arguments->getOpt('search')) {
            $query->whereLike('name', $search);
        }

        // addTable() only underlines the first row - it does not derive headers,
        // so the caller supplies them.
        $rows = [[
            'id'        => 'ID',
            'name'      => 'NAME',
            'client_id' => 'CLIENT ID',
            'state'     => 'STATE',
            'owner'     => 'OWNER',
            'redirect'  => 'REDIRECT URI',
        ]];

        foreach ($query->order('id', 'desc')->rows() as $app) {
            $rows[] = [
                'id'        => $app->get('id'),
                'name'      => $app->get('name'),
                'client_id' => $app->get('client_id'),
                'state'     => $app->get('state') == 1 ? 'enabled' : 'disabled',
                'owner'     => $app->get('created_by'),
                'redirect'  => strtok((string) $app->get('redirect_uri'), "\n"),
            ];
        }

        if (count($rows) == 1) {
            $this->output->addLine('No applications found.', 'warning');
            return;
        }

        $this->output->addTable($rows, true);
        $this->output->addLine((count($rows) - 1) . ' application(s)');
    }

    /**
     * Show one application in detail
     *
     * @param   int  $id
     * @return  void
     **/
    private function detail($id)
    {
        $app = $this->requireApp($id);

        $details = [
            'id'           => $app->get('id'),
            'name'         => $app->get('name'),
            'description'  => $app->get('description'),
            'client_id'    => $app->get('client_id'),
            'redirect_uri' => $app->get('redirect_uri'),
            'grant_types'  => $app->get('grant_types'),
            'state'        => $app->get('state') == 1 ? 'enabled' : 'disabled',
            'created'      => $app->get('created'),
            'created_by'   => $app->get('created_by'),
        ];

        // A client secret is a credential: print it only when asked, so it does
        // not end up in scrollback or CI logs by accident.
        if ($this->arguments->getOpt('show-secret')) {
            $details['client_secret'] = $app->get('client_secret');
        } else {
            $details['client_secret'] = '(hidden - pass --show-secret)';
        }

        $this->output->addRawFromAssocArray($details);
    }

    /**
     * Register a new application
     *
     * @museDescription  Registers a new OAuth application
     *
     * @return  void
     **/
    public function register()
    {
        $name = $this->arguments->getOpt('name');
        $uri  = $this->arguments->getOpt('redirect-uri');
        $user = $this->arguments->getOpt('user');
        // The model rejects an empty description, so it is required here too
        // rather than failing validation after the fact.
        $desc = $this->arguments->getOpt('description');

        if (!$name || !$uri || !$user || !$desc) {
            $this->output->error('Required: --name, --description, --redirect-uri and --user');
        }

        $owner = $this->resolveUser($user);

        // Multiple redirect URIs are newline separated in storage; accept them
        // comma separated on a command line, where newlines are painful.
        $uris = implode("\n", array_map('trim', explode(',', $uri)));

        $app = Application::blank();
        $app->set([
            'name'         => $name,
            'description'  => $desc,
            'redirect_uri' => $uris,
            'created_by'   => $owner->get('id'),
            'state'        => 1,
            'hub_account'  => 0,
        ]);

        if ($grants = $this->arguments->getOpt('grants')) {
            $app->set('grant_types', $grants);
        }

        if (!$app->save()) {
            foreach ($app->getErrors() as $error) {
                $this->output->addLine($error, 'error');
            }
            $this->output->error('Could not register the application.');
        }

        $this->output->addLine('Registered application #' . $app->get('id'), 'success');
        $this->output->addRawFromAssocArray([
            'name'         => $app->get('name'),
            'client_id'    => $app->get('client_id'),
            'redirect_uri' => $app->get('redirect_uri'),
            'grant_types'  => $app->get('grant_types'),
            'owner'        => $owner->get('username') . ' (#' . $owner->get('id') . ')',
        ]);

        $this->output->addSpacer();
        $this->output->addLine('Retrieve the client secret with:', 'info');
        $this->output->addLine('  muse developer:oauth show --id=' . $app->get('id') . ' --show-secret');
    }

    /**
     * Update an existing application
     *
     * @museDescription  Updates an application's name, description, redirect URI or grants
     *
     * @return  void
     **/
    public function update()
    {
        $app = $this->requireApp($this->arguments->getOpt('id'));

        $changed = [];

        foreach (['name' => 'name', 'description' => 'description', 'grants' => 'grant_types'] as $opt => $field) {
            if (($value = $this->arguments->getOpt($opt)) !== null && $value !== false) {
                $app->set($field, $value);
                $changed[] = $field;
            }
        }

        if ($uri = $this->arguments->getOpt('redirect-uri')) {
            $app->set('redirect_uri', implode("\n", array_map('trim', explode(',', $uri))));
            $changed[] = 'redirect_uri';
        }

        if (!$changed) {
            $this->output->error('Nothing to change. Pass --name, --description, --redirect-uri or --grants');
        }

        if (!$app->save()) {
            foreach ($app->getErrors() as $error) {
                $this->output->addLine($error, 'error');
            }
            $this->output->error('Could not update the application.');
        }

        $this->output->addLine('Updated #' . $app->get('id') . ': ' . implode(', ', $changed), 'success');
    }

    /**
     * Issue a new client secret
     *
     * @museDescription  Rotates an application's client credentials
     *
     * @return  void
     **/
    public function rotate()
    {
        $app = $this->requireApp($this->arguments->getOpt('id'));

        if (!$this->confirm('Rotating invalidates the current credentials. Continue?')) {
            $this->output->addLine('Aborted.');
            return;
        }

        $previous = $app->get('client_id');

        // Application::newClientSecret() mints a fresh client_id as well, so
        // every existing token and stored credential for this app stops working.
        $secret = $app->newClientSecret();

        $app->set('client_secret', $secret);

        if (!$app->save()) {
            $this->output->error('Could not rotate credentials.');
        }

        $this->output->addLine('Rotated credentials for #' . $app->get('id'), 'success');
        $this->output->addRawFromAssocArray([
            'previous_client_id' => $previous,
            'client_id'          => $app->get('client_id'),
        ]);
        $this->output->addLine('Existing access and refresh tokens for this client are now invalid.', 'warning');
    }

    /**
     * Enable an application
     *
     * @museDescription  Enables an application
     *
     * @return  void
     **/
    public function enable()
    {
        $this->setState(1, 'Enabled');
    }

    /**
     * Disable an application
     *
     * @museDescription  Disables an application
     *
     * @return  void
     **/
    public function disable()
    {
        $this->setState(0, 'Disabled');
    }

    /**
     * Delete an application
     *
     * @museDescription  Deletes an application
     *
     * @return  void
     **/
    public function delete()
    {
        $app = $this->requireApp($this->arguments->getOpt('id'));

        if (!$this->confirm('Delete "' . $app->get('name') . '" (#' . $app->get('id') . ')?')) {
            $this->output->addLine('Aborted.');
            return;
        }

        if (!$app->destroy()) {
            $this->output->error('Could not delete the application.');
        }

        $this->output->addLine('Deleted application #' . $app->get('id'), 'success');
    }

    /**
     * Set application state
     *
     * @param   int     $state
     * @param   string  $verb
     * @return  void
     **/
    private function setState($state, $verb)
    {
        $app = $this->requireApp($this->arguments->getOpt('id'));
        $app->set('state', $state);

        if (!$app->save()) {
            $this->output->error('Could not update the application.');
        }

        $this->output->addLine($verb . ' application #' . $app->get('id'), 'success');
    }

    /**
     * Load an application or fail
     *
     * @param   int  $id
     * @return  object
     **/
    private function requireApp($id)
    {
        if (!$id) {
            $this->output->error('Required: --id');
        }

        $app = Application::oneOrNew($id);

        if (!$app->get('id')) {
            $this->output->error('No application with id ' . $id);
        }

        return $app;
    }

    /**
     * Resolve a username or numeric id to a user
     *
     * @param   mixed  $identifier
     * @return  object
     **/
    private function resolveUser($identifier)
    {
        $user = is_numeric($identifier)
            ? User::oneOrNew($identifier)
            : User::oneByUsername($identifier);

        if (!$user || !$user->get('id')) {
            $this->output->error('No such user: ' . $identifier);
        }

        return $user;
    }

    /**
     * Prompt before a destructive change, unless --force was given
     *
     * @param   string  $question
     * @return  bool
     **/
    private function confirm($question)
    {
        if ($this->arguments->getOpt('force')) {
            return true;
        }

        // Output::isInteractive() reports the renderer's mode, not whether a
        // terminal is attached, so ask the stream directly.
        if (function_exists('posix_isatty') && !posix_isatty(STDIN)) {
            $this->output->error('Refusing to continue without a terminal. Pass --force.');
        }

        return strtolower(trim($this->output->getResponse($question . ' [y/N]'))) === 'y';
    }

    /**
     * Help
     *
     * @return  void
     **/
    public function help()
    {
        $this->output
            ->addOverview('Register and manage OAuth client applications.')
            ->addTasks($this)
            ->addArgument(
                '--id=: Application id',
                'Identifies the application for show, update, rotate, enable, disable and delete.',
                'muse developer:oauth show --id=42',
            )
            ->addArgument(
                '--name=: Application name',
                'Required when registering.',
                'muse developer:oauth register --name="nanoDT" --user=jdoe --redirect-uri=http://127.0.0.1:47813/callback',
            )
            ->addArgument(
                '--redirect-uri=: Redirect URI(s)',
                'Comma separate multiple URIs. Each must be a valid absolute URL; note that "localhost" '
                    . 'fails validation, so use 127.0.0.1 for native loopback redirects.',
                '--redirect-uri=http://127.0.0.1:47813/callback',
            )
            ->addArgument(
                '--user=: Owning user',
                'Username or numeric id. The application is created as this user.',
                '--user=jdoe',
            )
            ->addArgument(
                '--grants=: Grant types',
                'Space separated. Defaults to "authorization_code password refresh_token".',
                '--grants="authorization_code refresh_token"',
            )
            ->addArgument(
                '--description=: Description',
                'Free text shown in the developer UI. Required when registering; the model rejects an empty value.',
            )
            ->addArgument(
                '--show-secret: Reveal the client secret',
                'Client secrets are hidden by default so they do not leak into logs.',
            )
            ->addArgument(
                '--user=: Filter by owner (show)',
                'Restricts the listing to one user\'s applications.',
            )
            ->addArgument(
                '--search=: Filter by name (show)',
                'Substring match against the application name.',
            )
            ->addArgument(
                '--force: Skip confirmation',
                'Required for rotate and delete when there is no terminal attached.',
            );
    }
}
