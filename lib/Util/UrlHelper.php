<?php

namespace Ridibooks\Platform\Common\Util;

class UrlHelper
{
    /**
     * @param string $url
     * @param string $msg
     * @return string
     */
    public static function printAlertRedirect($url, $msg)
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"></head><body><script>';
        if (!empty($msg)) {
            $html .= "alert(" . json_encode($msg) . ");";
        }
        $html .= "location.href=" . json_encode($url) . ";";
        $html .= "</script></body></html>\n";

        return $html;
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param string $msg
     * @return string
     */
    public static function printAlertPostRedirect($url, $parameters, $msg)
    {
        // script 생성
        $script = '';
        if (!empty($msg)) {
            $script .= "alert(" . json_encode($msg) . ");";
        }
        $script .= 'document.getElementById("post_redirect_form").submit()';

        // form 생성
        $form = '<form id="post_redirect_form" action="' . $url . '" method="post">';
        foreach ($parameters as $key => $value) {
            if (!is_array($value)) {
                $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '""/>';
            } else {
                foreach ($value as $inner_value) {
                    $form .= '<input type="hidden" name="' . $key . '" value="' . $inner_value . '""/>';
                }
            }
        }
        $form .= '</form>';

        return '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"></head><body>'
            . "<script>{$script}</script>{$form}</body></html>\n";
    }

    /**
     * @param string $msg
     * @return string
     */
    public static function printAlertHistoryBack($msg)
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"></head><body><script>';
        if (!empty($msg)) {
            $html .= "alert(" . json_encode($msg) . ");";
        }
        $html .= "history.go(-1);";
        $html .= "</script></body></html>\n";

        return $html;
    }

    /**
     * @param string $msg
     * @return string
     */
    public static function printConfirm($msg)
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"></head><body><script>';
        if (!empty($msg)) {
            $html .= "if(!confirm(" . json_encode($msg) . ")){ history.go(-1) };";
        }
        $html .= "</script></body></html>\n";

        return $html;
    }

    /**
     * confirm 창 출력 후 지정한 주소들로 리다이렉트
     * @param string $msg
     * @param string $confirm_url
     * @param string $cancel_url
     *
     * @return string
     */
    public static function printConfirmRedirect(string $msg, string $confirm_url, string $cancel_url): string
    {
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"></head><body><script>';

        $html .= 'if (confirm(' . json_encode($msg) . ')) { ';
        $html .= '	location.href=' . json_encode($confirm_url) . ';';
        $html .= '}';
        $html .= 'else { ';
        $html .= '	location.href=' . json_encode($cancel_url) . ';';
        $html .= '}';

        $html .= '</script></body></html>';

        return $html;
    }

    /**
     * @param array $query_map
     * @param array $replace
     * @return string
     */
    public static function buildQuery($query_map, $replace)
    {
        foreach ($replace as $k => $v) {
            $query_map[$k] = $v;
        }

        $query_string = http_build_query($query_map);
        if (!empty($query_string)) {
            $query_string = '?' . $query_string;
        }

        return $query_string;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getUrlWithoutHost($url)
    {
        if ($url === null) {
            return null;
        }

        $parsed_referer = parse_url($url);

        return $parsed_referer['path'] . '?' . $parsed_referer['query'];
    }
}
