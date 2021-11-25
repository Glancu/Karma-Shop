<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\SonataMediaMedia;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\Request;

class ImageService
{
    private ImageProvider $imageProvider;

    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function getImageNameAndUrl(Request $request, SonataMediaMedia $image, $format = 'big'): array
    {
        $provider = $this->imageProvider;
        $format = $provider->getFormatName($image, $format);
        $imageUrl = $provider->generatePublicUrl($image, $format);

        $baseUrl = $request ? ($request->getSchemeAndHttpHost() . $request->getBaseUrl()) : '';

        $fullImageUrl = $baseUrl . $imageUrl;

        return [
            'name' => $image->getName(),
            'url' => $fullImageUrl
        ];
    }
}
