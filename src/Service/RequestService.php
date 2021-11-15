<?php

namespace App\Service;

use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;

class RequestService
{
    public static function isDataProcessingAgreementValid($dataProcessingAgreement): bool
    {
        return $dataProcessingAgreement === true || $dataProcessingAgreement === "true" ||
            $dataProcessingAgreement === 1 || $dataProcessingAgreement === "1";
    }

    /**
     * @param string $requestContent
     *
     * @return array
     *
     * @throws JsonException
     * @throws Exception
     */
    public static function getArrayDataFromRequestContent(string $requestContent): array
    {
        $data = json_decode($requestContent, true, 512, JSON_THROW_ON_ERROR);
        if(!$data || !is_array($data)) {
            throw new Exception('Not found data');
        }

        foreach($data as $key => $item) {
            if($key !== 'dataProcessingAgreement' && !is_array($item)) {
                $data[$key] = htmlspecialchars($item);
            } elseif($key === 'dataProcessingAgreement') {
                $data[$key] = self::isDataProcessingAgreementValid($item);
            }
        }

        return $data;
    }

    public static function checkIsAllRequiredDataFound(array $data, array $requiredData): array
    {
        $notFoundRequiredDataArr = [];

        foreach($requiredData as $item) {
            if(!array_key_exists($item, $data)) {
               $notFoundRequiredDataArr[] = $item;
            }
        }

        return $notFoundRequiredDataArr;
    }

    /**
     * @param string $requestContent
     * @param array $requiredDataFromContent
     *
     * @return array|JsonResponse
     *
     * @throws JsonException
     */
    public function validRequestContentAndGetData(string $requestContent, array $requiredDataFromContent)
    {
        $data = self::getArrayDataFromRequestContent($requestContent);

        $notFoundRequiredData = self::checkIsAllRequiredDataFound($data, $requiredDataFromContent);
        if(!empty($notFoundRequiredData) && is_array($notFoundRequiredData)) {
            $errorsList = ['error' => true, 'message' => 'Property ' . $notFoundRequiredData[0] . ' cannot be null.'];

            return new JsonResponse($errorsList, 400);
        }

        return $data;
    }
}
