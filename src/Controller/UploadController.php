<?php

namespace App\Controller;

use App\Services\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    /**
     * @Route("/upload", name="uploadFile")
     * @param Request $request
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */
    public function uploadPhoto(Request      $request,
                          FileUploader $uploader,
                          LoggerInterface $logger): Response
    {
        $token = $request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token)) {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed", Response::HTTP_BAD_REQUEST);
        }

        $file = $request->files->get('myfile');

        if (empty($file)) {
            return new Response("No file specified",
                Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $uploader->upload($file);

        return new Response("File uploaded", Response::HTTP_OK);
    }
}