<?php
declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotNull(message="Product uuid cannot be null")
     */
    public string $productUuid;

    /**
     * @Assert\IsTrue(message="Accept terms before create comment")
     */
    public bool $dataProcessingAgreement;
}
