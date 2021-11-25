<?php
declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateCommentFormModel
{
    /**
     * @Assert\NotNull(message="Name cannot be null")
     */
    public string $name;

    /**
     * @Assert\NotNull(message="Email cannot be null")
     */
    public string $email;

    /**
     * @Assert\NotNull(message="Message cannot be null")
     */
    public string $message;

    /**
     * @Assert\IsTrue(message="Accept terms before create comment")
     */
    public bool $dataProcessingAgreement;

    public ?string $productUuid = '';

    public ?string $blogPostUuid = '';

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     */
    public function validateData(ExecutionContextInterface $context): void
    {
        if(!$this->productUuid && !$this->blogPostUuid) {
            $context
                ->buildViolation('Not found object to add comment')
                ->atPath('productUuid')
                ->atPath('blogPostUuid')
                ->addViolation();
        }
    }
}
