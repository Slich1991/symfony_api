<?php
// api/src/Controller/CreateProductImageAction.php

namespace App\Controller;

use App\Entity\ProductImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateProductImageAction extends AbstractController
{
    public function __invoke(Request $request): ProductImage
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $productImage = new ProductImage();
        $productImage->file = $uploadedFile;

        return $productImage;
    }
}