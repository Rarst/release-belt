<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    /** @var RouterInterface $router */
    private $router;

    /** @var UriInterface $url */
    private $url;

    public function __construct(RouterInterface $router, UriInterface $url)
    {
        $this->router = $router;
        $this->url    = $url;
    }

    public function getUrl(string $name, array $data = []): string
    {
        return (string)$this->url->withPath($this->router->pathFor($name, $data));
    }

    public function getFileUrl(string $vendor, string $file): string
    {
        return $this->getUrl('file', ['vendor' => $vendor, 'file' => $file]);
    }
}
