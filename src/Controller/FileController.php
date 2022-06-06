<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Release;
use Slim\Exception\HttpNotFoundException;
use Slim\Http\Response;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Handles the route for file downloads.
 */
class FileController
{
    protected FileModel $model;

    public function __construct(FileModel $model)
    {
        $this->model  = $model;
    }

    /**
     * Looks up the file and sends download response.
     *
     * @throws HttpNotFoundException
     */
    public function __invoke(Request $request, Response $response, string $vendor, string $file): Response
    {
        $sendFile = $this->model->getFile($vendor, $file);

        if (! $sendFile->isReadable()) {
            throw new HttpNotFoundException($request);
        }

        return $response->withFileDownload($sendFile->getRealPath());
    }
}
