<?php

namespace Ridibooks\Platform\Common;

/**
 * @deprecated
 */
class ResponseHeaderUtils
{
    /**
     * 윈도우환경에서 html table 전송하되, 엑셀에서 열릴 수 있게할떄 설정하는 헤더
     *
     * @param $file_name
     */
    public static function setXlsHeader($file_name)
    {
        header("Content-Type: application/vnd.ms-excel;charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$file_name.xls\"");
        header('Cache-Control: max-age=0');
    }
}
