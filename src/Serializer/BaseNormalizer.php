<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Comment;
use App\Entity\ProductReview;
use App\Entity\SonataMediaMedia;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseNormalizer
{
    private ImageProvider $imageProvider;
    private RequestStack $request;

    public function __construct(
        ImageProvider $imageProvider,
        RequestStack $request
    ) {
        $this->imageProvider = $imageProvider;
        $this->request = $request;
    }

    public function getUrlImages($images, $format = 'big'): array
    {
        $imagesArr = [];

        /**
         * @var SonataMediaMedia $image
         */
        foreach ($images as $image) {
            $request = $this->request->getCurrentRequest();
            $provider = $this->imageProvider;
            $format = $provider->getFormatName($image, $format);
            $imageUrl = $provider->generatePublicUrl($image, $format);

            $baseUrl = $request ? ($request->getSchemeAndHttpHost() . $request->getBaseUrl()) : '';

            $fullImageUrl = $baseUrl . $imageUrl;

            $imagesArr[] = [
                'name' => $image->getName(),
                'url' => $fullImageUrl
            ];
        }

        return $imagesArr;
    }

    public function generateCommentsForSingleObject($topic, array $data): array
    {
        if (isset($data['comments'])) {
            foreach ($data['comments'] as $commentKey => $comment) {
                if (isset($comment['enable']) && $comment['enable'] === false) {
                    unset($data['comments'][$commentKey]);
                } else {
                    /**
                     * @var Comment|ProductReview $commentObj
                     */
                    $commentObj = $topic->getComments()->filter(function ($commentObject) use ($comment) {
                        return $commentObject->getUuid() === $comment['uuid'];
                    })->first();
                    if ($commentObj && $commentObj->getCreatedAt()) {
                        $data['comments'][$commentKey]['createdAt'] = $commentObj->getCreatedAt()
                                                                                 ->format('d-m-Y H:i:s');
                    } else {
                        unset($data['comments'][$commentKey]);
                    }
                }
            }

            $data['comments'] = array_values($data['comments']);
        }

        return $data;
    }
}
