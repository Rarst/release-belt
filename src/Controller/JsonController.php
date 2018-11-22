<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Handles packages.json route.
 */
class JsonController
{
    protected $data;

    /**
     * JsonController constructor.
     *
     * @param array[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns JSON response with packages data.
     */
    public function __invoke(ServerRequestInterface $request, Response $response): ResponseInterface
    {
        return $response->withJson($this->data);
    }
}
