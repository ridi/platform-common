<?php

namespace Ridibooks\Platform\Common;

use Ridibooks\Exception\MsgException;

class ValidationUtils
{
	/**
	 * 입력된 필드가 null이거나 비어있을(empty) 경우 exception
	 * @param string $field
	 * @param string $msg
	 * @throws \Ridibooks\Exception\MsgException
	 */
	public static function checkNullField($field, $msg)
	{
		if (StringUtils::isEmpty($field)) {
			throw new MsgException($msg);
		}
	}

	/**
	 * 입력된 필드가 숫자가 아닐 경우 exception
	 * @param $field
	 * @param string $msg
	 * @throws \Ridibooks\Exception\MsgException
	 */
	public static function checkNumberField($field, $msg)
	{
		if ((StringUtils::isEmpty($field) === false) && !is_numeric($field)) {
			throw new MsgException($msg);
		}
	}

	/**
	 * 입력된 필드의 최소 길이보다 작을 경우 exception
	 * @param object $field
	 * @param int $minLength
	 * @param string $msg
	 * @throws \Ridibooks\Exception\MsgException
	 */
	public static function checkMinLength($field, $minLength, $msg)
	{
		if ((StringUtils::isEmpty($field) === false) && mb_strlen($field) < $minLength) {
			throw new MsgException($msg);
		}
	}

	/**
	 * 입력된 필드의 길이가 정해진 길이와 다를 경우 exception
	 * @param object $field
	 * @param int $length
	 * @param string $msg
	 * @throws \Ridibooks\Exception\MsgException
	 */
	public static function checkLength($field, $length, $msg)
	{
		if ((StringUtils::isEmpty($field) === false) && mb_strlen($field) != $length) {
			throw new MsgException($msg);
		}
	}

	/**
	 * 입력된 필드의 값이 적합한 datetime 형식이 아닐 경우 exception
	 * @param string $field
	 * @param string $format
	 * @param string $msg
	 * @throws MsgException
	 */
	public static function checkDatetimeFormat($field, $format, $msg)
	{
		$date = date($format, strtotime($field));
		if ($field !== $date) {
			throw new MsgException($msg);
		}
	}

	/**
	 * @param string $start
	 * @param string $end
	 * @param string $message
	 * @throws MsgException
	 */
	public static function checkDatetimePeriod($start, $end, $message)
	{
		$timestamp_start = strtotime($start);
		$timestamp_end = strtotime($end);
		if ($timestamp_end < $timestamp_start) {
			throw new MsgException($message);
		}
	}

	/**
	 * ISBN10 값 유효성 체크한다.
	 * https://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-10_check_digit_calculation
	 * @param $isbn
	 * @throws MsgException
	 */
	public static function checkIsbn10Number($isbn)
	{
		$isbn = trim($isbn);
		if (mb_strlen($isbn) !== 10 || preg_match('/0{10}/', $isbn)) {
			throw new MsgException("ISBN10 형식에 맞지 않습니다.");
		}

		$total = 0;
		for ($i = 0; $i < 9; $i++) {
			$digit = intval(substr($isbn, $i, 1));
			$total += ((10 - $i) * $digit);
		}

		$check_sum = (11 - ($total % 11)) % 11;
		if ($check_sum === 10) {
			$check_sum = 'X';
		}

		if ($check_sum != substr($isbn, 9)) {
			throw new MsgException("ISBN10 형식에 맞지 않습니다.");
		}
	}

	/**
	 * ISBN13 값 유효성 체크한다.
	 * http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-13_check_digit_calculation
	 * @param string $isbn
	 * @throws MsgException
	 */
	public static function checkIsbn13Number($isbn)
	{
		$isbn = trim($isbn);
		if (mb_strlen($isbn) !== 13 || preg_match('/0{13}/', $isbn)) {
			throw new MsgException("ISBN13 형식에 맞지 않습니다.");
		}

		if (!is_numeric($isbn)) {
			throw new MsgException('ISBN13 형식에 맞지 않습니다.');
		}

		$total = 0;

		for ($i = 0; $i < 12; $i++) {
			$digit = intval(substr($isbn, $i, 1));
			$total += ($i % 2 === 0) ? $digit : $digit * 3;
		}

		$check_sum = 10 - ($total % 10);
		if ($check_sum === 10) {
			$check_sum = 0;
		}

		if ($check_sum !== intval(substr($isbn, -1))) {
			throw new MsgException("ISBN13 형식에 맞지 않습니다.");
		}
	}

	/**
	 * ECN 값 유효성 체크한다.
	 *
	 * ex) ecn sample
	 * ECN-0102-2008-000-123456789
	 * I410-ECN-0199-2009-657-010848674
	 * @param $ecn
	 * @throws MsgException
	 */
	public static function checkEcn($ecn)
	{
		$ecn = trim(StringUtils::removeHyphen($ecn));
		/*
		 * ECN을 더이상 사용하지 않고, 그 대안으로 UCI를 사용하도록 하였다.
		 * 기존에 ECN을 발급받은 도서들의 경우
		 * UCI를 발급받지 않고,
		 * ECN 번호 앞에 I410을 붙여 UCI 번호로 하기로 하였다.
		 */
		$ecn = str_replace('I410', '', $ecn);

		if (mb_strlen($ecn) !== 23 || preg_match('/0{23}/', $ecn)) {
			throw new MsgException('ECN 형식에 맞지 않습니다.');
		}
	}

	/**
	 * ISSN 값 유효성 체크한다.
	 * https://en.wikipedia.org/wiki/International_Standard_Serial_Number#Code_format
	 * @param $issn
	 * @throws MsgException
	 */
	public static function checkIssn($issn)
	{
		$issn = trim(StringUtils::removeHyphen($issn));

		if (mb_strlen($issn) !== 8 || preg_match('/0{8}/', $issn)) {
			throw new MsgException('ISSN 형식에 맞지 않습니다.');
		}

		$total = 0;

		for ($i = 0; $i < 7; $i++) {
			$digit = intval(substr($issn, $i, 1));
			$total += ((8 - $i) * $digit);
		}

		$check_sum = 11 - ($total % 11);
		if ($check_sum === 10) {
			$check_sum = 'X';
		}

		if ($check_sum != substr($issn, -1)) {
			throw new MsgException("ISSN 형식에 맞지 않습니다.");
		}
	}

	public static function checkHtml($html, $msg)
	{
		if (HtmlUtils::isValidHtmlTag($html, HtmlUtils::$cms_allowable_tags) === false) {
			throw new MsgException($msg);
		}
	}

	public static function checkPhoneNumber(string $phone, string $msg)
	{
		$expression = '/(0[0-9]{1,2}-?)([0-9]{3,4}-?)([0-9]{4})$/';
		if (!preg_match($expression, $phone)) {
			throw new MsgException($msg);
		}
	}

	public static function checkMailAddress(string $mail, string $msg)
	{
		$expression = '/([_A-Za-z0-9]+)@((?:[A-Za-z0-9]+\.){1,3}+)([A-Za-z]{2,5})$/';
		if (!preg_match($expression, $mail)) {
			throw new MsgException($msg);
		}
	}
}
