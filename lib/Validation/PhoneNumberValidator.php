<?php

namespace Ridibooks\Platform\Common\Validation;

use Ridibooks\Platform\Common\Util\ValidationUtils;

class PhoneNumberValidator
{
    /**
     * @param string $phone_number
     * @return bool
     */
    public static function isValid($phone_number)
    {
        return ValidationUtils::match('/^\d{2,4}-\d{3,4}-\d{4}$/', $phone_number);
    }
}
