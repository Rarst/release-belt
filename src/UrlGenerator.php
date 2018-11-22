<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouterInterface;

/**
 * Concrete implementation for the absolute URL generation.
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /** @var RouterInterface $router */
    private $router;

    /** @var UriInterface $url */
    private $url;

    /**
     * UrlGenerator constructor.
     */
    public function __construct(RouterInterface $router, UriInterface $url)
    {
        $this->router = $router;
        $this->url    = $url->withUserInfo('');
    }

    /**
     * @inheritdoc
     */
    public function getUrl(string $name, array $data = []): string
    {
        return (string)$this->url->withPath($this->router->pathFor($name, $data));
    }

    /**
     * @inheritdoc
     */
    public function getFileUrl(string $vendor, string $file): string
    {
        return $this->getUrl('file', ['vendor' => $vendor, 'file' => $file]);
    }
}
