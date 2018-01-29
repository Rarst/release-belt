<?php
namespace Rarst\ReleaseBelt;

use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Model\IndexModel;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function getHtml(Application $app)
    {
        /** @var IndexModel $indexModel */
        $indexModel = $app['model.index'];

        return $app->render('index', $indexModel->getContext());
    }

    public function getJson(Application $app)
    {
        return $app->json($app['data']);
    }

    public function getFile(Application $app, $vendor, $file)
    {
        /** @var FileModel $fileModel */
        $fileModel = $app['model.file'];

        try {
            $sendFile = $fileModel->getFile($vendor, $file);
        } catch (FileNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $app->logDownload($sendFile);

        return $app->sendFile($sendFile->getRealPath());
    }
}
