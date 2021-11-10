<?php

namespace App\Service;

class RequestService
{
    public static function isDataProcessingAgreementValid($dataProcessingAgreement): bool
    {
        return $dataProcessingAgreement === true || $dataProcessingAgreement === "true" ||
            $dataProcessingAgreement === 1 || $dataProcessingAgreement === "1";
    }
}
