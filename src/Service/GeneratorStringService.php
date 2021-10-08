<?php
declare(strict_types=1);

namespace App\Service;

use Exception;

final class GeneratorStringService
{
    /**
     * @param int $length
     *
     * @return string
     *
     * @throws Exception
     */
    public static function generateString(int $length): string
    {
        $alphabet = implode(range(0, 9))
            . implode(range('a', 'z'))
            . implode(range('A', 'Z'));

        $alphabetMaxIndex = strlen($alphabet) - 1;
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $index = random_int(0, $alphabetMaxIndex);
            $randomString .= $alphabet[$index];
        }

        return $randomString;
    }
}
