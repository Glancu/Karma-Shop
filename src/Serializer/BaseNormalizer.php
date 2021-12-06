<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Comment;
use App\Entity\ProductReview;
use App\Entity\SonataMediaMedia;
use App\Service\ImageService;
use Sonata\MediaBundle\Provider\ImageProvider;

class BaseNormalizer
{
    private ImageProvider $imageProvider;
    private ImageService $imageService;

    public function __construct(
        ImageProvider $imageProvider,
        ImageService $imageService
    ) {
        $this->imageProvider = $imageProvider;
        $this->imageService = $imageService;
    }

    public function getUrlImages($images, $format = 'big'): array
    {
        $imagesArr = [];

        /**
         * @var SonataMediaMedia $image
         */
        foreach ($images as $image) {
            $imagesArr[] = $this->imageService->getImageNameAndUrl($image, $format);
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
