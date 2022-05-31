<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Symfony\Component\Finder\Finder;
use Tuupola\Middleware\HttpBasicAuthentication;

/**
 * Implements HTTP authentication.
 */
class AuthenticationProvider
{
    private ContainerInterface $container;

    /**
     * Does necessary registrations on the app instance.
     */
    public function boot(App $app): void
    {
        $container       = $app->getContainer();
        $this->container = $container;

        $userHashes = $this->getUserHashes();

        if (empty($userHashes)) {
            return;
        }

        $app->add(new HttpBasicAuthentication([
            'secure' => false,
            'users'  => $userHashes,
            'before' => $this->before(),
        ]));
    }

    /**
     * Returns a closure to use in authentication middleware.
     *
     * The closure add username attribute to request and applies permissions.
     */
    private function before(): \Closure
    {
        $auth = $this; // We need this because middleware binds the closure to its own object.

        return function (ServerRequestInterface $request, array $arguments) use ($auth): ServerRequestInterface {
            $username = $arguments['user'] ?? '';

            $auth->applyPermissions(
                $auth->container->get('finder'),
                $auth->getPermissions($auth->container->get('users'), $username)
            );

            return $request->withAttribute('username', $username);
        };
    }

    /**
     * Retrieves a set of user names with password hashes.
     */
    protected function getUserHashes(): array
    {
        /** @var string[] $users */
        $users = $this->container->has('http.users') ? $this->container->get('http.users') : [];

        if ($users) {
            trigger_error('`http.users` option is deprecated in favor of `users`.', E_USER_DEPRECATED);
        }

        foreach ($this->container->get('users') as $login => $data) {
            $users[$login] = $data['hash'] ?? '';
        }

        return array_filter($users);
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
     */
    protected function applyPermissions(Finder $finder, array $permissions): Finder
    {
        foreach ($permissions['allow'] as $path) {
            $finder->path((string)$path);
        }

        foreach ($permissions['disallow'] as $path) {
            $finder->notPath((string)$path);
        }

        return $finder;
    }
}
