<?php

namespace Rarst\ReleaseBelt\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        $app['security.firewalls'] = [];
    }

    public function boot(Application $app)
    {
        $userHashes = $this->getUserHashes($app);

        if (empty($userHashes)) {
            return;
        }

        $app['security.firewalls'] = [
            'composer' => [
                'pattern' => '^.*$',
                'http'    => true,
                'users'   => $userHashes,
            ],
        ];

        $app->extend('finder', function (Finder $finder, Application $app) {

            /** @var array[][] $users */
            $users = $app['users'];
            /** @var Request $request */
            $request = $app['request_stack']->getCurrentRequest();
            $user    = $request->getUser();

            return $this->applyPermissions($finder, $this->getPermissions($users, $user));
        });
    }

    protected function getUserHashes(Application $app)
    {
        $users = [];

        if ( ! empty($app['http.users'])) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);

            foreach ($app['http.users'] as $login => $hash) {
                $users[$login] = ['ROLE_COMPOSER', $hash];
            }
        }

        foreach ($app['users'] as $login => $data) {
            $users[$login] = ['ROLE_COMPOSER', $data['hash']];
        }

        return $users;
    }

    protected function getPermissions(array $users, $user)
    {
        return [
            // TODO use ?? when bumped requirements to PHP 7.
            'allow'    => empty($users[$user]['allow']) ? [] : $users[$user]['allow'],
            'disallow' => empty($users[$user]['disallow']) ? [] : $users[$user]['disallow'],
        ];
    }

    protected function applyPermissions(Finder $finder, array $permissions)
    {
        foreach ($permissions['allow'] as $path) {
            $finder->path($path);
        }

        foreach ($permissions['disallow'] as $path) {
            $finder->notPath($path);
        }

        return $finder;
    }
}
