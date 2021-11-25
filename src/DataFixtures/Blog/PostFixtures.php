<?php

namespace App\DataFixtures\Blog;

use App\DataFixtures\AdminUserFixtures;
use App\DataFixtures\CommentFixtures;
use App\Entity\AdminUser;
use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Entity\SonataMediaMedia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
            AdminUserFixtures::class,
            CommentFixtures::class
        ];
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 40; $i++) {
            $product = new BlogPost(
                $faker->text(50),
                $faker->text(500),
                $faker->text(7000),
                $this->getBlogCategory(),
                $this->getAdminUser(),
                $this->createImage(),
                $this->getTags(),
                $this->getComments()
            );

            $product->setViews(random_int(3000, 8000));

            $manager->persist($product);
        }

        $manager->flush();
    }

    private static function getRandomValueFromArray(array $array): string
    {
        return $array[array_rand($array, 1)];
    }

    private function getAdminUser(): AdminUser
    {
        return $this->entityManager->getRepository('App:AdminUser')->findOneBy([]);
    }

    private function getBlogCategory(): BlogCategory
    {
        $namesArr = CategoryFixtures::getNamesArr();
        $name = self::getRandomValueFromArray($namesArr);

        return $this->entityManager->getRepository('App:BlogCategory')->findOneBy([
            'name' => $name
        ]);
    }

    private function createImageMediaBundle(
        UploadedFile $file,
        $context = 'default'
    ): SonataMediaMedia {
        $em = $this->entityManager;

        $media = new SonataMediaMedia();
        $media->setContext($context);
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent($file);

        $em->persist($media);
        $em->flush();

        return $media;
    }

    /**
     * @return SonataMediaMedia
     *
     * @throws Exception
     */
    private function createImage(): SonataMediaMedia
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');

        $randomImageInt = random_int(1, 5);

        $imagePath = $rootDir . "/public/assets/img/blog/m-blog-${randomImageInt}.jpg";

        $uploadedFile = new UploadedFile(
            $imagePath,
            'blog_image.jpg',
            'image/jpg'
        );

        return $this->createImageMediaBundle($uploadedFile);
    }

    private function getTags(): array
    {
        $items = $this->entityManager->getRepository('App:BlogTag')->findAll();

        $tags = [];

        foreach (array_rand($items, 2) as $key) {
            $tags[] = $items[$key];
        }

        return $tags;
    }

    private function getComments(): array
    {
        $items = $this->entityManager->getRepository('App:Comment')->findAll();

        $comments = [];

        foreach (array_rand($items, 3) as $key) {
            $comments[] = $items[$key];
        }

        return $comments;
    }
}
