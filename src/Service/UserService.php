<?php

namespace App\Service;

use App\Entity\ClientUser;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserService
{
    private JWTEncoderInterface $encoder;

    private UserPasswordEncoderInterface $userPasswordEncoderInterface;

    private EntityManagerInterface $entityManager;

    public function __construct(
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $userPasswordEncoderInterface,
        EntityManagerInterface $entityManager
    ) {
        $this->encoder = $encoder;
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
        $this->entityManager = $entityManager;
    }

    public function decodeUserByJWTToken(string $token): ?array
    {
        try {
            return $this->encoder->decode($token);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param string $email
     *
     * @return ClientUser
     *
     * @throws Exception
     */
    public function createUser(string $email, ?string $password = null): ClientUser
    {
        $clientUser = new ClientUser($email, '');

        $password = $password ?: bin2hex(random_bytes(10));

        $encodedPassword = $this->userPasswordEncoderInterface->encodePassword($clientUser, $password);
        $clientUser->setPassword($encodedPassword);

        $this->entityManager->persist($clientUser);
        $this->entityManager->flush();

        return $clientUser;
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
