<?php

namespace App\DataFixtures\Shop;

use App\Entity\ShopBrand;
use App\Entity\ShopCategory;
use App\Entity\ShopColor;
use App\Entity\ShopProduct;
use App\Entity\SonataMediaMedia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductFixtures extends Fixture implements DependentFixtureInterface
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
            SpecificationFixtures::class,
            ColorFixtures::class
        ];
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $priceNet = random_int(2000, 5000);
            $priceGross = $priceNet + random_int(2000, 8000);

            $product = new ShopProduct(
                'product ' . $i,
                $priceNet,
                $priceGross,
                500,
                'description of product ' . $i,
                $this->getShopBrand(),
                $this->getShopCategory(),
                $this->getShopProductSpecifications(),
                [$this->createProductImage()],
                true,
                [$this->getShopColor()]
            );
            $manager->persist($product);
        }

        $manager->flush();
    }

    private static function getRandomValueFromArray(array $array): string
    {
        return $array[array_rand($array, 1)];
    }

    private function getShopBrand(): ShopBrand
    {
        $shopBrandsNames = BrandFixtures::getBrandsNamesArr();
        $shopBrandName = self::getRandomValueFromArray($shopBrandsNames);

        return $this->entityManager->getRepository('App:ShopBrand')->findOneBy([
            'title' => $shopBrandName
        ]);
    }

    private function getShopCategory(): ShopCategory
    {
        $shopCategoriesNames = CategoryFixtures::getCategoriesNamesArr();
        $shopCategoryName = self::getRandomValueFromArray($shopCategoriesNames);

        return $this->entityManager->getRepository('App:ShopCategory')->findOneBy([
            'title' => $shopCategoryName
        ]);
    }

    private function getShopProductSpecifications(): array
    {
        $shopProductSpecifications = $this->entityManager->getRepository('App:ShopProductSpecification')->findAll();

        $shopProductSpecificationsArr = [];

        foreach (array_rand($shopProductSpecifications, 2) as $key) {
            $shopProductSpecificationsArr[] = $shopProductSpecifications[$key];
        }

        return $shopProductSpecificationsArr;
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
    private function createProductImage(): SonataMediaMedia
    {
        $rootDir = $this->parameterBag->get('kernel.project_dir');

        $randomProductInt = random_int(1, 8);

        $productImagePath = $rootDir . "/public/assets/img/product/p${randomProductInt}.jpg";

        $uploadedFile = new UploadedFile(
            $productImagePath,
            'product_image.jpg',
            'image/jpg'
        );

        return $this->createImageMediaBundle($uploadedFile);
    }

    private function getShopColor(): ShopColor
    {
        $shopColorNames = ColorFixtures::getColorsNamesArr();
        $shopColorName = self::getRandomValueFromArray($shopColorNames);

        return $this->entityManager->getRepository('App:ShopColor')->findOneBy([
            'name' => $shopColorName
        ]);
    }
}
