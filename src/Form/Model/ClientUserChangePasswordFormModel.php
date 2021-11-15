<?php
declare(strict_types=1);

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ClientUserChangePasswordFormModel
{
    /**
     * @Assert\NotNull(message="Old password cannot be null")
     */
    public string $oldPassword;

    /**
     * @Assert\NotNull(message="New password cannot be null")
     */
    public string $newPassword;

    /**
     * @Assert\NotNull(message="New password repeat cannot be null")
     */
    public string $newPasswordRepeat;
}
