<?php
declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateOrderFormModel
{
    /**
     * @Assert\NotNull(message="Personal data cannot be null")
     */
    public array $personalData;

    /**
     * @Assert\NotNull(message="Method payment cannot be null")
     */
    public ?string $methodPayment = '';

    /**
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}"
     * )
     */
    public bool $isCustomCorrespondence;

    /**
     * @Assert\NotNull(message="Not found products to create order")
     */
    public array $products;

    /**
     * @Assert\IsTrue(message="Accept terms before create order")
     */
    public bool $dataProcessingAgreement;

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     */
    public function validateData(ExecutionContextInterface $context): void
    {
        $personalData = $this->personalData;
        $requiredKeysForPersonalData = [
            'firstName',
            'lastName',
            'phoneNumber',
            'email',
            'addressLineFirst',
            'addressLineSecond',
            'city'
        ];

        foreach($requiredKeysForPersonalData as $key) {
            if(!array_key_exists($key, $personalData)) {
                $context
                    ->buildViolation("${key} is required to create order")
                    ->atPath("personalData[${key}]")
                    ->addViolation();
            }
        }

        $requiredKeysForCustomCorrespondence = [
            'firstNameCorrespondence',
            'lastNameCorrespondence',
            'addressLineFirstCorrespondence',
            'addressLineSecondCorrespondence',
            'cityCorrespondence'
        ];

        if($this->isCustomCorrespondence === true) {
            foreach($requiredKeysForCustomCorrespondence as $key) {
                if(!array_key_exists($key, $personalData)) {
                    $context
                        ->buildViolation("${key} is required to create order")
                        ->atPath("personalData[${key}]")
                        ->addViolation();
                }
            }
        }

        if(!$this->methodPayment || !in_array($this->methodPayment, Order::getMethodPaymentsArr(), true)) {
            $context
                ->buildViolation('Payment method not found')
                ->atPath('methodPayment')
                ->addViolation();
        }

        if($this->products) {
            $products = $this->products;
            if($products) {
                foreach($products as $productData) {
                    $productQuantity = isset($productData, $productData['quantity']) ?
                        (int)$productData['quantity'] :
                        null;
                    if(!$productQuantity || $productQuantity < 1) {
                        $context
                            ->buildViolation('Quantity of product cannot be smaller than one')
                            ->atPath('products')
                            ->addViolation();
                    }

                    $productUuid = $productData['uuid'] ?? null;
                    if(!$productUuid) {
                        $context
                            ->buildViolation('Product was not found')
                            ->atPath('products')
                            ->addViolation();
                    }
                }
            } else {
                $context
                    ->buildViolation('Not found products to create order')
                    ->atPath('products')
                    ->addViolation();
            }
        } else {
            $context
                ->buildViolation('Not found products to create order2')
                ->atPath('products')
                ->addViolation();
        }
    }
}
