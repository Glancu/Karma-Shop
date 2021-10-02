<?php
declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductReviewFormModel
{
    /**
     * @Assert\NotNull(message="PName cannot be null")
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
     * @Assert\NotNull(message="Rating cannot be null")
     * @Assert\GreaterThanOrEqual(
     *     value="1",
     *     message="Rating cannot be lower than 1"
     * )
     */
    public int $rating;

    /**
     * @Assert\NotNull(message="Product uuid cannot be null")
     */
    public string $productUuid;

    /**
     * @Assert\IsTrue(message="Accept terms before create review")
     */
    public bool $dataProcessingAgreement;
}
