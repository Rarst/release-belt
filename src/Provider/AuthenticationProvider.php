<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Rarst\ReleaseBelt\Application;
use Slim\Container;
use Symfony\Component\Finder\Finder;
use Tuupola\Middleware\HttpBasicAuthentication;

/**
 * Implements HTTP authentication.
 */
class AuthenticationProvider
{
    /**
     * Does necessary registrations on the app instance.
     */
    public function boot(Application $app): void
    {
        $container  = $app->getContainer();
        $userHashes = $this->getUserHashes($container);

        if (empty($userHashes)) {
            return;
        }

        $app->add(new HttpBasicAuthentication([
            'secure' => false,
            'users'  => $userHashes,
        ]));

        $container->extend('finder', function (Finder $finder, Container $container) {

            return $this->applyPermissions($finder, $this->getPermissions($container['users'], $container['username']));
        });
    }

    /**
     * Retrieves a set of user names with password hashes from a container instance.
     */
    protected function getUserHashes(Container $container): array
    {
        $users = [];

        if (! empty($container['http.users'])) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);

            foreach ($container['http.users'] as $login => $hash) {
                $users[$login] = $hash;
            }
        }

        foreach ($container['users'] as $login => $data) {
            if (! empty($data['hash'])) {
                $users[$login] = $data['hash'];
            }
        }

        return $users;
    }

    /**
     * Retrieves package access permissions for a specific user.
     *
     * @param array[] $users
     */
    protected function getPermissions(array $users, string $user): array
    {
        return [
            'allow'    => $users[$user]['allow'] ?? [],
            'disallow' => $users[$user]['disallow'] ?? [],
        ];
    }

    /**
     * Applies access permissions on a Finder instance for package lookup..
     *
     * @param array[] $permissions
     */
    protected function applyPermissions(Finder $finder, array $permissions): Finder
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
