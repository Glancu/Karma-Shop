<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\BlogPost;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BlogSerializeDataResponse
{
    private BlogNormalizer $blogNormalizer;
    private SerializerInterface $serializer;

    public function __construct(BlogNormalizer $blogNormalizer, SerializerInterface $serializer) {
        $this->blogNormalizer = $blogNormalizer;
        $this->serializer = $serializer;
    }

    /**
     * @param array $posts
     * @param int $countPosts
     * @param array $parameters
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogPostsData(array $posts, int $countPosts = 0, array $parameters = []): string
    {
        $items = [];

        /**
         * @var BlogPost $post
         */
        foreach ($posts as $post) {
            $data = $this->blogNormalizer->normalizeBlogPostsList($post, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_post_list',
                    'blog_category_list',
                    'blog_tag_list'
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);

            $items[] = $data;
        }

        $return = [
            'countItems' => $countPosts,
            'items' => $items
        ];

        if($parameters['category']) {
            $return['categoryName'] = $items[0]['category']['name'];
        }

        if($parameters['tag']) {
            foreach($items[0]['tags'] as $tag) {
                if($parameters['tag'] === $tag['slug']) {
                    $return['tagName'] = $tag['name'];
                }
            }
        }

        return $this->serializer->serialize($return, 'json');
    }

    /**
     * @param BlogPost $post
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function getSingleBlogPostData(BlogPost $post): array
    {
        return $this->blogNormalizer->normalizeSingleBlogPost($post, 'json', [
            'groups' => [
                'uuid_trait',
                'enable_trait',
                'price_trait',
                'blog_post_show',
                'blog_category_show',
                'blog_tag_show',
                'comment'
            ],
            'datetime_format' => 'Y-m-d H:i:s'
        ]);
    }

    /**
     * @param array $posts
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogPopularPosts(array $posts): string
    {
        $items = [];

        /**
         * @var BlogPost $post
         */
        foreach ($posts as $post) {
            $data = $this->blogNormalizer->normalizeBlogPopularPosts($post, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_post_list',
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);

            $items[] = $data;
        }

        return $this->serializer->serialize($items, 'json');
    }

    /**
     * @param array $categories
     *
     * @return string
     *
     * @throws ExceptionInterface
     */
    public function getBlogCategoriesLatestData(array $categories): string
    {
        $items = [];

        foreach ($categories as $category) {
            $items[] = $this->blogNormalizer->normalizeBlogCategoriesLatest($category, 'json', [
                'groups' => [
                    'uuid_trait',
                    'enable_trait',
                    'blog_category_show'
                ],
                'datetime_format' => 'Y-m-d H:i:s'
            ]);
        }

        return $this->serializer->serialize($items, 'json');
    }
}
