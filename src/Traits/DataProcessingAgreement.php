<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait DataProcessingAgreement {
    /**
     * @var bool
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="boolean")
     */
    private bool $dataProcessingAgreement;

    /**
     * DataProcessingAgreement constructor.
     */
    public function __construct() {
        $this->dataProcessingAgreement = false;
    }

    /**
     * @return bool
     */
    public function isDataProcessingAgreement(): bool {
        return $this->dataProcessingAgreement;
    }

    /**
     * @param bool $dataProcessingAgreement
     */
    public function setDataProcessingAgreement(bool $dataProcessingAgreement): void {
        $this->dataProcessingAgreement = $dataProcessingAgreement;
    }
}
