<?php

namespace App\Serializer;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BlogNormalizer extends BaseNormalizer
{
    private UrlGeneratorInterface $router;
    private ObjectNormalizer $normalizer;
    private ImageProvider $imageProvider;
    private RequestStack $request;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        ImageProvider $imageProvider,
        RequestStack $request
    ) {
        parent::__construct($imageProvider, $request);

        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->imageProvider = $imageProvider;
        $this->request = $request;
    }

    /**
     * @param BlogPost $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
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

    /**
     * @param BlogPost $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeSingleBlogPost(BlogPost $topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        $data['image'] = current($this->getUrlImages([$topic->getImage()]));

        $data = $this->generateCommentsForSingleObject($topic, $data);

        $data['createdAt'] = $topic->getCreatedAt()->format('d-m-Y');

        return $data;
    }

    /**
     * @param BlogPost $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeBlogPopularPosts(BlogPost $topic, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($topic, $format, $context);
        if (isset($data['enable']) && $data['enable'] === false) {
            return [];
        }

        $data['image'] = current($this->getUrlImages([$topic->getImage()]));

        unset($data['shortContent'], $data['category'], $data['tags']);

        return $data;
    }

    /**
     * @param BlogCategory $topic
     * @param null $format
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function normalizeBlogCategoriesLatest(BlogCategory $topic, $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($topic, $format, $context);
    }
}
