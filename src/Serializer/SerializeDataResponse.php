<?php

namespace App\Serializer;

use App\Entity\ClientUser;
use App\Entity\Contact;
use App\Entity\Newsletter;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializeDataResponse
{
    /**
     * @param ClientUser $clientUser
     *
     * @return string
     */
    public function getClientUserData(ClientUser $clientUser): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback
            ],
        ];

        $ignoredAttributes = [
            'password',
            'orders',
            'roles',
            'comments',
            'id',
            'salt',
            'username',
            'passwordChangedAt'
        ];

        return $this->getSerializedData($clientUser, $ignoredAttributes, $defaultContext);
    }

    /**
     * @param Newsletter $newsletter
     *
     * @return string
     */
    public function getNewsletterData(Newsletter $newsletter): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $ignoredAttributes = [
            'name',
            'id'
        ];

        if ($newsletter->getName()) {
            unset($ignoredAttributes[0]);
        }

        return $this->getSerializedData($newsletter, $ignoredAttributes, $defaultContext);
    }

    /**
     * @param Contact $contact
     *
     * @return string
     */
    public function getContactData(Contact $contact): string
    {
        $dateCallback = static function ($innerObject) {
            return self::getDateCallback($innerObject);
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback
            ],
        ];

        $ignoreAttributes = [
            'id'
        ];

        return $this->getSerializedData($contact, $ignoreAttributes, $defaultContext);
    }

    /**
     * @param $object
     * @param array|null $ignoredAttributes
     * @param array $defaultContext
     *
     * @return string|null
     */
    private function getSerializedData($object, ?array $ignoredAttributes, $defaultContext = []): ?string
    {
        if (!$object) {
            return null;
        }

        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);
        $encoder = new JsonEncoder();

        $serializer = new Serializer([$normalizer], [$encoder]);

        return $serializer->serialize($object, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => $ignoredAttributes
        ]);
    }

    /**
     * @param $innerObject
     *
     * @return string
     */
    private static function getDateCallback($innerObject): string
    {
        return $innerObject instanceof DateTime ? $innerObject->format(DateTimeInterface::ATOM) : '';
    }
}
