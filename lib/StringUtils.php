<?php

namespace Ridibooks\Platform\Common;

class StringUtils
{
	/* DB에서 찾아변경할때
	SELECT id, title, unhex(replace(hex(title),'C2A0','20'))
	FROM tb_book
	WHERE hex(title) LIKE concat('%C2A0%')
	*/
	const UNICODE_NON_BREAKING_SPACE = "\xc2\xa0";
	const UNICODE_ZERO_WIDTH_SPACE = "\xe2\x80\x8b";

	public static function removeSpecificCharaters($string)
	{
		return preg_replace('/[^\w' . preg_quote('|') . ']/u', '', $string);
	}

	public static function removeTag($string)
	{
		$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);
		$string = str_replace(" ", "&nbsp;", $string);

		return $string;
	}

	/**
	 * 해당 문자열이 비어있는가 체크.
	 * null과 비어있는 문자(' ')를 체크 하기 위해 사용
	 * @param $string
	 * @return bool
	 */
	public static function isEmpty($string)
	{
		if (is_null($string) || trim($string) === '') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 해당 문자열이 주민등록번호인지 체크.
	 * 년도는 00 ~ 99 [0-9]{2}
	 * 월은 01 ~ 12 0[1-9] || 1[012]
	 * 일은 01 ~ 31 0[1-9] || 1[0-9] || 2[0-9] || 3[01]
	 * 숫자6자리와 숫자7자리 사이의 - 는 없을수도 있음 -?
	 * 뒷7자리 숫자의 첫자리는 성별 [012349]
	 * 성별 뒤의 5자리 숫자는 주소지 등 [0-9]{5}
	 * 마지막의 숫자는 앞의 숫자가 유효한지를 나타내는 check digit 를 나타내지만 정규식에서 체크하는 것은 한계
	 * [0-9]
	 *
	 * @param string $jumin
	 *
	 * @return bool
	 */
	public static function isJumin(string $jumin)
	{
		return preg_match("/[0-9]{2}(0[1-9]|1[012])(0[1-9]|1[0-9]|2[0-9]|3[01])-?[012349][0-9]{5}[0-9]/i", $jumin) > 0;
	}

	/**주민등록번호를 출력형식으로 변환한다.
	 * @deprecated
	 * @param string $jumin
	 * @return string
	 */
	public static function maskJuminForDisplay($jumin)
	{
		return substr($jumin, 0, 6) . "-" . substr($jumin, 6, 1) . "******";
	}

	public static function normalizeString($str, $is_single_line = false)
	{
		$str = self::normalizeSpace($str, $is_single_line);
		/*
		 * Unicode의 형식을 NFC로 맞춘다.
		 * iconv -l을 사용해보았을때 MAC에서만 UTF-8-MAC을 지원하기 때문에 iconv를 사용하지 않는다.
		 * 이슈: https://app.asana.com/0/9476649488676/157381871168492
		 */
		if (!\Normalizer::isNormalized($str)) {
			$normalized_string = \Normalizer::normalize($str);
			if ($normalized_string !== false) {
				$str = $normalized_string;
			}
		}

		return $str;
	}

	private static function normalizeSpace($str, $is_single_line = false)
	{
		if ($is_single_line) {
			$replace = [
				StringUtils::UNICODE_ZERO_WIDTH_SPACE,
				StringUtils::UNICODE_NON_BREAKING_SPACE,
				"\r",
				"\t",
				"\n"
			];
		} else {
			$replace = [StringUtils::UNICODE_ZERO_WIDTH_SPACE, StringUtils::UNICODE_NON_BREAKING_SPACE];
		}
		$str = str_replace($replace, ' ', $str);

		return $str;
	}

	/**UTF-8 non-breaking-space 제거
	 * @param string $string
	 * @return string
	 */
	public static function removeNonBreakingSpace($string)
	{
		return str_replace(self::UNICODE_NON_BREAKING_SPACE, "", $string);
	}

	/**UTF-8 zero width space 제거
	 * @param string $string
	 * @return string
	 */
	public static function removeZeroWidthSpace($string)
	{
		return str_replace(self::UNICODE_ZERO_WIDTH_SPACE, "", $string);
	}

	/**하이픈(-) 제거한다.
	 * @param $string
	 * @return string
	 */
	public static function removeHyphen($string)
	{
		return trim(str_replace('-', '', $string));
	}

	/**TODO ArrayUtil 만들어서 이동 && method 설명 추가할 것
	 * @see http://php.net/manual/en/class.simplexmlelement.php
	 * @param $xmlstring string
	 * @return array
	 */
	public static function xml2array($xmlstring)
	{
		$result = [];
		self::normalizeSimpleXML(simplexml_load_string($xmlstring, null, LIBXML_NOCDATA), $result);

		return $result;
	}

	/**TODO ArrayUtil 만들어서 이동 && method 설명 추가할 것
	 * @param $obj
	 * @param $result
	 */
	private static function normalizeSimpleXML($obj, &$result)
	{
		$data = $obj;
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$res = null;
				self::normalizeSimpleXML($value, $res);
				if (($key == '@attributes') && ($key)) {
					$result = $res;
				} else {
					$result[$key] = $res;
				}
			}
		} else {
			$result = $data;
		}
	}

	/**
	 * @param $explain
	 * @return mixed
	 *
	 * <p><강추> 아스란 연대기</p>
	 * 위와 같은 html과 text가 섞여서 들어오는 입력(내부나 외부-북큐브)에서 HTML 제거용
	 */
	public static function stripTagsOnlyEnglishBegin($explain)
	{
		return preg_replace('/<(\/?)[a-z][^<>]*>/i', '', $explain);
	}

	/**TODO method 설명 추가할 것
	 * @param $comma_separated
	 * @return array
	 */
	public static function commaSeparatedToArray($comma_separated)
	{
		return array_filter(explode(',', $comma_separated));
	}

	public static function explodeByLine($new_values_by_line)
	{
		return preg_split("/[^\n\S]*\r?\n[^\n\S]*/", $new_values_by_line);
	}

	public static function decodeCsv($input)
	{
		preg_match_all("/\s*\r?\n|,|\"[^\"]*\"|[^\",\r\n]+/s", $input, $mats, PREG_SET_ORDER);
		$rets = [];
		$ret = [];
		$cursor = '';
		foreach ($mats as $mat) {
			$str = $mat[0];
			if (substr($str, -1) == "\n") {
				$ret[] = $cursor;
				$rets[] = $ret;
				$ret = [];
				$cursor = '';
			} elseif ($str[0] == ',') {
				$ret[] = $cursor;
				$cursor = '';
			} elseif ($str[0] == '"') {
				if (strlen($cursor) == 0) {
					$cursor = substr($str, 1, -1);
				} else {
					$cursor .= '"' . substr($str, 1, -1);
				}
			} else {
				$cursor = $str;
			}
		}
		if (strlen($cursor)) {
			$ret[] = $cursor;
			$rets[] = $ret;
		}

		return $rets;
	}

	public static function encodeCsv($input)
	{
		$ret = '';
		foreach ($input as $row) {
			foreach ($row as $dat) {
				$ret .= '"' . str_replace('"', '""', $dat) . '",';
			}
			$ret .= "\n";
		}

		return $ret;
	}

	public static function basenameUtf8($input)
	{
		$input = str_replace('\\', '\/', $input);
		preg_match('/\/?([^\/]+)$/', $input, $mat);

		return $mat[1];
	}

	/**
	 * 배열을 chunk로 나누어 implode 시킨다.
	 * ex) ['A1', 'A2', 'A3', 'B1', 'B2', 'B3']의 배열에서
	 * (';', "\n", 3, $pieces) => "A1;A2;A3\nB1;B2;B3"
	 * @param string $glue_in_chunk chunk 내부에서의 구분자
	 * @param string $glue_between_chunks chunk 끼리의 구분자
	 * @param int $chunk_size chunk 내부 사이즈
	 * @param array $pieces
	 * @return string
	 * @throws \Exception
	 */
	public static function implodeByChunk($glue_in_chunk, $glue_between_chunks, $chunk_size, array $pieces)
	{
		if (count($pieces) % $chunk_size !== 0) {
			throw new \Exception('chunk size error');
		}

		$result = '';

		// 구분자 혼동방지를 위한 replace
		foreach ($pieces as &$str) {
			$str = str_replace($glue_in_chunk, '', $str);
			$str = str_replace($glue_between_chunks, '', $str);
		}

		$rows = array_chunk($pieces, $chunk_size);
		foreach ($rows as $row) {
			if (!array_filter($row)) {
				continue;
			}

			$result .= trim(implode($glue_in_chunk, $row)) . $glue_between_chunks;
		}

		return trim($result);
	}

	/**
	 * 두 문자열을 서로 치환한다.
	 * ex) &lt;b&gt;<b> => <b>&lt;b&gt;
	 * @param string $string
	 * @param string $sub_string1
	 * @param string $sub_string2
	 * @return string
	 */
	public static function swapTwoSubStrings($string, $sub_string1, $sub_string2)
	{
		$length = strlen($string);
		for ($i = 0; $i <= $length; $i++) {
			if (substr($string, $i, strlen($sub_string1)) == $sub_string1) {
				$string = substr_replace($string, $sub_string2, $i, strlen($sub_string1));
				$length -= strlen($sub_string1);
				$length += strlen($sub_string2);
				$i += strlen($sub_string2) - 1;    // for문내에서 $i++이기에
			} elseif (substr($string, $i, strlen($sub_string2)) == $sub_string2) {
				$string = substr_replace($string, $sub_string1, $i, strlen($sub_string2));
				$length -= strlen($sub_string2);
				$length += strlen($sub_string1);
				$i += strlen($sub_string1) - 1;    // for문내에서 $i++이기에
			}
		}

		return $string;
	}

	private static $non_printable_ascii = null;
	private static $unicode_non_breaking_space = "\xc2\xa0";
	private static $unicode_zero_width_space = "\xe2\x80\x8b";
	private static $unicode_bom_utf8 = "\xef\xbb\xbf";

	public static function removeUnnecessaryCharacter($string)
	{
		self::initializeNonPrintableAscii();
		$removes = self::$non_printable_ascii;
		$removes[] = self::$unicode_bom_utf8;
		$removes[] = self::$unicode_non_breaking_space;
		$removes[] = self::$unicode_zero_width_space;

		return str_replace($removes, "", $string);
	}

	public static function removeNonPrintableAscii($string)
	{
		self::initializeNonPrintableAscii();

		return str_replace(self::$non_printable_ascii, "", $string);
	}

	private static function initializeNonPrintableAscii()
	{
		if (self::$non_printable_ascii === null) {
			$__non_printable_ascii = [
				0,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
				11,
				12,
				14,
				15,
				16,
				17,
				18,
				19,
				20,
				21,
				22,
				23,
				24,
				25,
				26,
				27,
				28,
				29,
				30,
				31,
				127,
			];
			foreach ($__non_printable_ascii as $k => $v) {
				$__non_printable_ascii[$k] = chr($v);
			}
			self::$non_printable_ascii = $__non_printable_ascii;
		}
	}
}
