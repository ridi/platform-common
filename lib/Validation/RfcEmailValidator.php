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
        $grammar['NO-WS-CTL'] = '[\x01-\x08\x0B\x0C\x0E-\x19\x7F]';
        $grammar['WSP'] = '[ \t]';
        $grammar['CRLF'] = '(?:\r\n)';
        $grammar['FWS'] = '(?:(?:' . $grammar['WSP'] . '*' . $grammar['CRLF'] . ')?' . $grammar['WSP'] . ')';
        $grammar['text'] = '[\x00-\x08\x0B\x0C\x0E-\x7F]';
        $grammar['quoted-pair'] = '(?:\\\\' . $grammar['text'] . ')';
        $grammar['ctext'] = '(?:' . $grammar['NO-WS-CTL'] . '|[\x21-\x27\x2A-\x5B\x5D-\x7E])';

        $grammar['ccontent'] = '(?:' . $grammar['ctext'] . '|' . $grammar['quoted-pair'] . '|(?1))';
        $grammar['comment'] = '(\((?:' . $grammar['FWS'] . '|' . $grammar['ccontent'] . ')*' . $grammar['FWS'] . '?\))';
        $grammar['CFWS'] = '(?:(?:' . $grammar['FWS'] . '?' . $grammar['comment'] . ')*(?:(?:' . $grammar['FWS'] . '?' . $grammar['comment'] . ')|' . $grammar['FWS'] . '))';
        $grammar['qtext'] = '(?:' . $grammar['NO-WS-CTL'] . '|[\x21\x23-\x5B\x5D-\x7E])';
        $grammar['qcontent'] = '(?:' . $grammar['qtext'] . '|' . $grammar['quoted-pair'] . ')';
        $grammar['quoted-string'] = '(?:' . $grammar['CFWS'] . '?"' . '(' . $grammar['FWS'] . '?' . $grammar['qcontent'] . ')*' . $grammar['FWS'] . '?"' . $grammar['CFWS'] . '?)';
        $grammar['atext'] = '[a-zA-Z0-9!#\$%&\'\*\+\-\/=\?\^_`\{\}\|~]';
        $grammar['dot-atom-text'] = '(?:' . $grammar['atext'] . '+' . '(\.' . $grammar['atext'] . '+)*)';
        $grammar['dot-atom'] = '(?:' . $grammar['CFWS'] . '?' . $grammar['dot-atom-text'] . '+' . $grammar['CFWS'] . '?)';
        $grammar['dtext'] = '(?:' . $grammar['NO-WS-CTL'] . '|[\x21-\x5A\x5E-\x7E])';

        $grammar['local-part'] = '(?:' . $grammar['dot-atom'] . '|' . $grammar['quoted-string'] . ')';
        $grammar['dcontent'] = '(?:' . $grammar['dtext'] . '|' . $grammar['quoted-pair'] . ')';
        $grammar['domain-literal'] = '(?:' . $grammar['CFWS'] . '?\[(' . $grammar['FWS'] . '?' . $grammar['dcontent'] . ')*?' . $grammar['FWS'] . '?\]' . $grammar['CFWS'] . '?)';
        $grammar['domain'] = '(?:' . $grammar['dot-atom'] . '|' . $grammar['domain-literal'] . ')';
        $grammar['addr-spec'] = '(?:' . $grammar['local-part'] . '@' . $grammar['domain'] . ')';

        return '/^' . $grammar['addr-spec'] . '$/D';
    }
}
