<?php

namespace Ridibooks\Platform\Common\Validation;

use Ridibooks\Platform\Common\Util\ValidationUtils;

class CustomEmailValidator
{
    public const DEFAULT_REGEXP = '/^[^@]{1,64}@[^@]{1,255}$/';
    public const USERNAME_REGEXP = '/^(([A-Za-z0-9!#$%&\'\*\+\/=\?\^_`{|}~-][A-Za-z0-9!#$%&\'\*\+\/=?^_`{|}~\.-]{0,63}))$/';
    public const DOMAIN_REGEXP = '/^(([A-Za-z0-9]+\.)|([A-Za-z0-9]+[A-Za-z0-9\-]+[A-Za-z0-9]\.)){1,}([A-Za-z]{1,})$/';
    public const GMAIL_DOMAIN = 'gmail.com';

    /**
     * 출처: http://www.linuxjournal.com/article/9585
     * 수정사항: preg_match 지원, IPv4 미지원, TLD 영문만, 특수문자 " 미지원
     * @param $email
     * @return bool
     */
    public static function isValid($email)
    {
        if (empty($email) || !is_string($email)) {
            return false;
        }

        if (!ValidationUtils::match(self::DEFAULT_REGEXP, $email)) {
            return false;
        }

        $email_exploded_by_at_sign = explode('@', $email);
        $local_parts = $email_exploded_by_at_sign[0];
        $domain_parts = $email_exploded_by_at_sign[1];

        // gmail 은 아이디에 마지막에 . 이 있을 수 있다. 그래서 검사 할 때 . 을 지우고 검사한다.

        $local_parts = self::generalizeLocalParts($local_parts, $domain_parts);
        $local_parts = explode('.', $local_parts);

        foreach ($local_parts as $local_part) {
            if (!ValidationUtils::match(self::USERNAME_REGEXP, $local_part)) {
                return false;
            }
        }

        // 도메인검사.  도메인은 영문과 숫자, 문자열 가운데 - 만 입력할 수 있다. TLD는 영문만 가능하다.
        if (!ValidationUtils::match(self::DOMAIN_REGEXP, $domain_parts)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $local_parts
     * @param string $domain_parts
     * @return string
     */
    public static function generalizeLocalParts($local_parts, $domain_parts)
    {
        if ($domain_parts === self::GMAIL_DOMAIN) {
            $local_parts = substr($local_parts, -1) === '.' ? substr($local_parts, 0, -1) : $local_parts;
        }

        return $local_parts;
    }
}
