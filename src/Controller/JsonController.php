<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles packages.json route.
 */
class JsonController
{
    /** @var array|array[] */
    protected $data;

    /** @var bool */
    private $debug;

    /**
     * JsonController constructor.
     *
     * @param array[] $data
     */
    public function __construct(array $data, bool $debug = false)
    {
        $this->data  = $data;
        $this->debug = $debug;
    }

    /**
     * Returns JSON response with packages data.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $prettyPrint = $this->debug ? JSON_PRETTY_PRINT : 0;

        $response->getBody()->write(json_encode($this->data, JSON_THROW_ON_ERROR | $prettyPrint));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
