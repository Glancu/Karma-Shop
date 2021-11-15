<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 *
 * Class OrderPersonalDataInfo
 *
 * @package App\Entity
 */
class OrderPersonalDataInfo
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $firstName = '';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $lastName = '';

    /**
     * @var null|string
     *
     * @ORM\Column(name="company_name", type="string", nullable=true)
     */
    private ?string $companyName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $phoneNumber = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $firstNameCorrespondence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $lastNameCorrespondence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="company_name_correspondence", type="string", length=255, nullable=true)
     */
    private ?string $companyNameCorrespondence;

    public function __construct(
        string $firstName,
        string $lastName,
        string $phoneNumber,
        ?string $companyName,
        ?string $firstNameCorrespondence,
        ?string $lastNameCorrespondence,
        ?string $companyNameCorrespondence
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->companyName = $companyName;
        $this->firstNameCorrespondence = $firstNameCorrespondence;
        $this->lastNameCorrespondence = $lastNameCorrespondence;
        $this->companyNameCorrespondence = $companyNameCorrespondence;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @param string|null $companyName
     */
    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getFirstNameCorrespondence(): ?string
    {
        return $this->firstNameCorrespondence;
    }

    /**
     * @param string|null $firstNameCorrespondence
     */
    public function setFirstNameCorrespondence(?string $firstNameCorrespondence): void
    {
        $this->firstNameCorrespondence = $firstNameCorrespondence;
    }

    /**
     * @return string|null
     */
    public function getLastNameCorrespondence(): ?string
    {
        return $this->lastNameCorrespondence;
    }

    /**
     * @param string|null $lastNameCorrespondence
     */
    public function setLastNameCorrespondence(?string $lastNameCorrespondence): void
    {
        $this->lastNameCorrespondence = $lastNameCorrespondence;
    }

    /**
     * @return string|null
     */
    public function getCompanyNameCorrespondence(): ?string
    {
        return $this->companyNameCorrespondence;
    }

    /**
     * @param string|null $companyNameCorrespondence
     */
    public function setCompanyNameCorrespondence(?string $companyNameCorrespondence): void
    {
        $this->companyNameCorrespondence = $companyNameCorrespondence;
    }
}
