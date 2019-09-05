<?php
namespace Ridibooks\Platform\Common\Validation;

use Ridibooks\Library\ExternalApi\MailgunHelper;

class ValidationHelper
{
    /**
     * 가장 많이 사용하는 CustomEmailValidator rule을 사용한다.
     * @param string $email
     * @return bool
     */
    public static function isValidEmailAddress($email)
    {
        return CustomEmailValidator::isValid($email);
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function isValidEmailByRfc($email)
    {
        return RfcEmailValidator::isValid($email);
    }

    /**
     * @param string $phone_number
     * @return bool
     */
    public static function isValidPhoneNumber($phone_number)
    {
        return PhoneNumberValidator::isValid($phone_number);
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isValidAbusiveWord($string)
    {
        return AbusiveWordValidator::isValid($string);
    }

    /**
     * @param $email
     * @return bool
     */
    public static function isValidEmailByMailgunApi($email)
    {
        return MailgunHelper::isVaildEmail($email);
    }
}
