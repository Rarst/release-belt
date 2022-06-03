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

    protected LoggerInterface $logger;

    public function __construct(FileModel $model, LoggerInterface $logger)
    {
        $this->model  = $model;
        $this->logger = $logger;
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

        /** @psalm-suppress DeprecatedMethod */
        $this->logFile($sendFile, $request);

        return $response->withFileDownload($sendFile->getRealPath());
    }

    /**
     * @deprecated 0.7:0.8
     */
    protected function logFile(SplFileInfo $file, Request $request): void
    {
        $release = new Release($file);

        $package = "{$release->vendor}/{$release->package}";
        $context = [
            'user'    => $request->getAttribute('username') ?: 'anonymous',
            'ip'      => $request->getAttribute('ip_address'),
            'vendor'  => $release->vendor,
            'package' => $release->package,
            'version' => $release->version,
        ];

        $this->logger->info($package, $context);
    }
}
