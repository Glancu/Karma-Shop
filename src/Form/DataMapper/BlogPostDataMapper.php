<?php

namespace App\Form\DataMapper;

use App\Entity\BlogPost;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Traversable;

class BlogPostDataMapper extends BaseDataMapper implements DataMapperInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormInterface[]|Traversable $forms
     * @param BlogPost $viewData
     */
    public function mapFormsToData($forms, &$viewData): void
    {
        $parameters = self::getParametersFromForm($forms);

        $author = $this->entityManager->getRepository('App:AdminUser')->findOneBy([
            'username' => $parameters['author']
        ]);

        if ($viewData->getId() === null) {
            $viewData = new BlogPost(
                $parameters['title'],
                $parameters['shortContent'],
                $parameters['longContent'],
                $parameters['category'],
                $author,
                $parameters['image'],
                $parameters['tags']->toArray()
            );
        } else {
            $viewData->setTitle($parameters['title']);
            $viewData->setShortContent($parameters['shortContent']);
            $viewData->setLongContent($parameters['longContent']);
            $viewData->setCategory($parameters['category']);
            $viewData->setImage($parameters['image']);

            foreach($parameters['tags']->toArray() as $tag) {
                $viewData->addTag($tag);
            }
        }
    }
}
