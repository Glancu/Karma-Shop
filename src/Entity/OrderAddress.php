<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 *
 * Class OrderAddress
 *
 * @package App\Entity
 */
class OrderAddress
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="street_first", type="string", length=255)
     */
    private string $streetFirst = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="street_second", type="string", length=255, nullable=true)
     */
    private ?string $streetSecond = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $city = '';

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $postalCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address_line_first_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $addressLineFirstCorrespondence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address_line_second_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $addressLineSecondCorrespondence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $cityCorrespondence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="postal_code_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $postalCodeCorrespondence;

    public function __construct(
        string $streetFirst,
        string $city,
        ?string $streetSecond = null,
        ?string $postalCode = null,
        ?string $addressLineFirstCorrespondence = null,
        ?string $addressLineSecondCorrespondence = null,
        ?string $cityCorrespondence = null,
        ?string $postalCodeCorrespondence = null
    ) {
        $this->streetFirst = $streetFirst;
        $this->city = $city;
        $this->streetSecond = $streetSecond;
        $this->postalCode = $postalCode;
        $this->addressLineFirstCorrespondence = $addressLineFirstCorrespondence;
        $this->addressLineSecondCorrespondence = $addressLineSecondCorrespondence;
        $this->cityCorrespondence = $cityCorrespondence;
        $this->postalCodeCorrespondence = $postalCodeCorrespondence;
    }

    /**
     * @return string
     */
    public function getStreetFirst(): string
    {
        return $this->streetFirst;
    }

    /**
     * @param string $streetFirst
     */
    public function setStreetFirst(string $streetFirst): void
    {
        $this->streetFirst = $streetFirst;
    }

    /**
     * @return string|null
     */
    public function getStreetSecond(): ?string
    {
        return $this->streetSecond;
    }

    /**
     * @param string|null $streetSecond
     */
    public function setStreetSecond(?string $streetSecond): void
    {
        $this->streetSecond = $streetSecond;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     */
    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getAddressLineFirstCorrespondence(): ?string
    {
        return $this->addressLineFirstCorrespondence;
    }

    /**
     * @param string|null $addressLineFirstCorrespondence
     */
    public function setAddressLineFirstCorrespondence(?string $addressLineFirstCorrespondence): void
    {
        $this->addressLineFirstCorrespondence = $addressLineFirstCorrespondence;
    }

    /**
     * @return string|null
     */
    public function getAddressLineSecondCorrespondence(): ?string
    {
        return $this->addressLineSecondCorrespondence;
    }

    /**
     * @param string|null $addressLineSecondCorrespondence
     */
    public function setAddressLineSecondCorrespondence(?string $addressLineSecondCorrespondence): void
    {
        $this->addressLineSecondCorrespondence = $addressLineSecondCorrespondence;
    }

    /**
     * @return string|null
     */
    public function getCityCorrespondence(): ?string
    {
        return $this->cityCorrespondence;
    }

    /**
     * @param string|null $cityCorrespondence
     */
    public function setCityCorrespondence(?string $cityCorrespondence): void
    {
        $this->cityCorrespondence = $cityCorrespondence;
    }

    /**
     * @return string|null
     */
    public function getPostalCodeCorrespondence(): ?string
    {
        return $this->postalCodeCorrespondence;
    }

    /**
     * @param string|null $postalCodeCorrespondence
     */
    public function setPostalCodeCorrespondence(?string $postalCodeCorrespondence): void
    {
        $this->postalCodeCorrespondence = $postalCodeCorrespondence;
    }
}
