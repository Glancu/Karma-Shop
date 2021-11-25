<?php

namespace App\Serializer;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\SonataMediaMedia;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BlogSerializer
{
    private $router;
    private $normalizer;
    private $imageProvider;
    private $request;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        ImageProvider $imageProvider,
        RequestStack $request
    ) {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->imageProvider = $imageProvider;
        $this->request = $request;
    }

    public function normalizeBlogPostsList(BlogPost $topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        $data['image'] = current($this->getUrlImages([$topic->getImage()]));

        $data['commentsCount'] = $topic->getComments()->count();

        $data['createdAt'] = $topic->getCreatedAt()->format('d-m-Y');

        return $data;
    }

    public function normalizeSingleBlogPost(BlogPost $topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        $data['image'] = current($this->getUrlImages([$topic->getImage()]));

        if (isset($data['comments'])) {
            foreach ($data['comments'] as $commentKey => $comment) {
                if (isset($comment['enable']) && $comment['enable'] === false) {
                    unset($data['comments'][$commentKey]);
                } else {
                    /**
                     * @var Comment $commentObj
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

        $data['createdAt'] = $topic->getCreatedAt()->format('d-m-Y');

        return $data;
    }

    public function normalizeBlogPopularPosts(BlogPost $topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        $data['image'] = current($this->getUrlImages([$topic->getImage()]));

        unset($data['shortContent']);
        unset($data['category']);
        unset($data['tags']);

        return $data;
    }

    public function normalizeBlogCategoriesLatest(BlogCategory $topic, $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($topic, $format, $context);
    }

    private function getUrlImages($images, $format = 'big'): array
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
}
