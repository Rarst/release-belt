<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Release;
use Slim\Exception\NotFoundException;
use Slim\Http\Stream;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Handles the route for file downloads and log access.
 */
class FileController
{
    protected $model;

    protected $logger;

    /** @var ServerRequestInterface $request */
    protected $request;

    /** @var ResponseInterface $response */
    protected $response;

    /**
     * FileController constructor.
     */
    public function __construct(FileModel $model, LoggerInterface $logger)
    {
        $this->model = $model;
        $this->logger = $logger;
    }

    /**
     * Looks up the file and sends download response.
     *
     * @throws NotFoundException
     *
     * @param string[] $args Route arguments parsed from the URL.
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $this->request  = $request;
        $this->response = $response;

        $sendFile = $this->model->getFile($args['vendor'], $args['file']);

        if (! $sendFile->isReadable()) {
            throw new NotFoundException($request, $response);
        }

        $this->logFile($sendFile);

        return $this->sendFile($sendFile);
    }

    /**
     * Logs the file download.
     */
    protected function logFile(SplFileInfo $file): void
    {
        $release = new Release($file);

        $package = "{$release->vendor}/{$release->package}";
        $context = [
            'user'    => $this->request->getAttribute('username') ?: 'anonymous',
            'ip'      => $this->request->getAttribute('ip_address'),
            'vendor'  => $release->vendor,
            'package' => $release->package,
            'version' => $release->version,
        ];

        $this->logger->info($package, $context);
    }

    /**
     * Returns streamed file download response.
     */
    protected function sendFile(SplFileInfo $file): ResponseInterface
    {
        $fileStream = fopen($file->getRealPath(), 'rb');

        return $this->response->withHeader('Content-Type', 'application/force-download')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="'.$file->getBasename().'"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody(new Stream($fileStream));
    }
}
