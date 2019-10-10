<?php

namespace Ridibooks\Platform\Common\Security;

/**
 * @deprecated SymmetricEncryption 사용
 */
class Encryption
{
    /**
     * AES256 암호화 후 BASE64로 인코딩 (기존 fnEncrypt)
     *
     * @param string $str
     * @param string $key
     * @param string $mode
     *
     * @return string
     */
    public static function encryptAESBase64(string $str, string $key, string $mode = MCRYPT_MODE_ECB): string
    {
        self::assertAllowedKeyLength($key);

        $text = $str;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, $mode);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypt_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, $mode, $iv);

        return trim(self::encodeBase64Safe($crypt_text));
    }

    /**
     * BASE64에 사용되는 문자열을 다른 문자열로 치환
     *
     * @param string $string
     *
     * @return string
     */
    private static function encodeBase64Safe(string $string): string
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

        return $data;
    }

    private static function assertAllowedKeyLength(string $key): void
    {
        $key_length = strlen($key);
        if ($key_length === 16 || $key_length === 24 || $key_length === 32) {
            return;
        }

        // TODO : 추후 예외로 변경한다.
        //throw new RidiErrorException('키 길이가 올바르지 않습니다. 키 길이는 16,24,32 중 하나이어야 합니다.');
        trigger_error('키 길이가 올바르지 않습니다. 키 길이는 16,24,32 중 하나이어야 합니다.');
    }
}
