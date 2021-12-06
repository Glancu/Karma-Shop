<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\SonataMediaMedia;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageService
{
    private ImageProvider $imageProvider;
    private RequestStack $requestStack;

    public function __construct(ImageProvider $imageProvider, RequestStack $requestStack)
    {
        $this->imageProvider = $imageProvider;
        $this->requestStack = $requestStack;
    }

    public function getImageNameAndUrl(SonataMediaMedia $image, $format = 'big'): array
    {
        $provider = $this->imageProvider;
        $format = $provider->getFormatName($image, $format);
        $imageUrl = $provider->generatePublicUrl($image, $format);

        $request = $this->requestStack->getCurrentRequest();

        $baseUrl = $request ? ($request->getSchemeAndHttpHost() . $request->getBaseUrl()) : '';

        $fullImageUrl = $baseUrl . $imageUrl;

        return [
            'name' => $image->getName(),
            'url' => $fullImageUrl
        ];
    }
}
