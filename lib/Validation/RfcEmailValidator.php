<?php

namespace Ridibooks\Platform\Common\Validation;

use Ridibooks\Platform\Common\Util\ValidationUtils;

class RfcEmailValidator
{
    /**
     * @param $email
     * @return bool
     */
    public static function isValid($email)
    {
        if (empty($email) || !is_string($email)) {
            return false;
        }

        return ValidationUtils::match(self::getRFCEmailValidationRegex(), $email);
    }

    /**
     * @return string
     */
    private static function getRFCEmailValidationRegex()
    {
        /*** Refer to RFC 2822 for ABNF grammar ***/
        $_grammar['NO-WS-CTL'] = '[\x01-\x08\x0B\x0C\x0E-\x19\x7F]';
        $_grammar['WSP'] = '[ \t]';
        $_grammar['CRLF'] = '(?:\r\n)';
        $_grammar['FWS'] = '(?:(?:' . $_grammar['WSP'] . '*' . $_grammar['CRLF'] . ')?' . $_grammar['WSP'] . ')';
        $_grammar['text'] = '[\x00-\x08\x0B\x0C\x0E-\x7F]';
        $_grammar['quoted-pair'] = '(?:\\\\' . $_grammar['text'] . ')';
        $_grammar['ctext'] = '(?:' . $_grammar['NO-WS-CTL'] . '|[\x21-\x27\x2A-\x5B\x5D-\x7E])';

        $_grammar['ccontent'] = '(?:' . $_grammar['ctext'] . '|' . $_grammar['quoted-pair'] . '|(?1))';
        $_grammar['comment'] = '(\((?:' . $_grammar['FWS'] . '|' . $_grammar['ccontent'] . ')*' . $_grammar['FWS'] . '?\))';
        $_grammar['CFWS'] = '(?:(?:' . $_grammar['FWS'] . '?' . $_grammar['comment'] . ')*(?:(?:' . $_grammar['FWS'] . '?' . $_grammar['comment'] . ')|' . $_grammar['FWS'] . '))';
        $_grammar['qtext'] = '(?:' . $_grammar['NO-WS-CTL'] . '|[\x21\x23-\x5B\x5D-\x7E])';
        $_grammar['qcontent'] = '(?:' . $_grammar['qtext'] . '|' . $_grammar['quoted-pair'] . ')';
        $_grammar['quoted-string'] = '(?:' . $_grammar['CFWS'] . '?"' . '(' . $_grammar['FWS'] . '?' . $_grammar['qcontent'] . ')*' . $_grammar['FWS'] . '?"' . $_grammar['CFWS'] . '?)';
        $_grammar['atext'] = '[a-zA-Z0-9!#\$%&\'\*\+\-\/=\?\^_`\{\}\|~]';
        $_grammar['dot-atom-text'] = '(?:' . $_grammar['atext'] . '+' . '(\.' . $_grammar['atext'] . '+)*)';
        $_grammar['dot-atom'] = '(?:' . $_grammar['CFWS'] . '?' . $_grammar['dot-atom-text'] . '+' . $_grammar['CFWS'] . '?)';
        $_grammar['dtext'] = '(?:' . $_grammar['NO-WS-CTL'] . '|[\x21-\x5A\x5E-\x7E])';

        $_grammar['local-part'] = '(?:' . $_grammar['dot-atom'] . '|' . $_grammar['quoted-string'] . ')';
        $_grammar['dcontent'] = '(?:' . $_grammar['dtext'] . '|' . $_grammar['quoted-pair'] . ')';
        $_grammar['domain-literal'] = '(?:' . $_grammar['CFWS'] . '?\[(' . $_grammar['FWS'] . '?' . $_grammar['dcontent'] . ')*?' . $_grammar['FWS'] . '?\]' . $_grammar['CFWS'] . '?)';
        $_grammar['domain'] = '(?:' . $_grammar['dot-atom'] . '|' . $_grammar['domain-literal'] . ')';
        $_grammar['addr-spec'] = '(?:' . $_grammar['local-part'] . '@' . $_grammar['domain'] . ')';

        return '/^' . $_grammar['addr-spec'] . '$/D';
    }
}
