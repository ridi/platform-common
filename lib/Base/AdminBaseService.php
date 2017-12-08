<?php

namespace Ridibooks\Platform\Common\Base;

use Ridibooks\Platform\Common\PagingUtil;

class AdminBaseService
{
    /**
     * 페이징 처리 html 태그를 만들어서 반환한다.
     * javascript는 fn_search로 통일시켰다.
     * @deprecated 파일분리, HTML과의 종속성 낮추거나 없애기 => PaginationHelper::getArgs() 사용
     *
     * @param int $total_count 리스트의 갯수
     * @param int $cur_page 현재 페이지
     * @param int $list_per_page 한 페이지에 보여질 리스트 갯수
     * @param int $line_per_page 한 리스트에 보여질 라인 수
     * @return string htmlTag
     */
    public static function getPagingTag($total_count, $cur_page, $list_per_page = null, $line_per_page = null)
    {
        $pagingUtil = new PagingUtil($total_count, $cur_page, $list_per_page, $line_per_page);
        return self::getPagingTagByPagingDto($pagingUtil);

    }

    /**
     * 페이징 처리 html 태그를 만들어서 반환한다.
     * @deprecated 파일분리, HTML과의 종속성 낮추거나 없애기 => PaginationHelper::getArgs() 사용
     *
     * @param \Ridibooks\Platform\Common\PagingUtil $pagingUtil
     * @return string htmlTag
     */
    public static function getPagingTagByPagingDto($pagingUtil)
    {
        $html = '';
        if ($pagingUtil->total > 0) {
            $html = '<ul>';

            if ($pagingUtil->cpage > $pagingUtil->list_per_page) {
                $html .= '<li><a href="javascript:void(0)" onClick="fn_search(' . 1 . ');">처음</a></li>';
                $html .= '<li><a href="javascript:void(0)" onClick="fn_search(' . $pagingUtil->prev_page_group . ');">이전</a></li>';
            }

            for ($i = $pagingUtil->start_page; $i <= $pagingUtil->end_page; $i++) {
                if ($i == $pagingUtil->cpage) {
                    $html .= '<li><a href="javascript:void(0)"  class="disabled btn-danger">' . $i . '</a></li>';
                } else {
                    $html .= '<li><a href="javascript:void(0)" onClick="fn_search(' . $i . ');">' . $i . '</a></li>';
                }
            }

            if ($pagingUtil->num_page_group < $pagingUtil->last_page_group) {
                $html .= '<li><a href="javascript:void(0)" onClick="fn_search(' . $pagingUtil->next_page_group . ');">다음</a></li>';
                $html .= '<li><a href="javascript:void(0)" onClick="fn_search(' . $pagingUtil->total_page . ');">끝</a></li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * 페이징 처리 html 태그를 만들어서 반환한다. (bootstrap 3에 맞추어 수정) by Sunghoon
     * @deprecated 파일분리, HTML과의 종속성 낮추거나 없애기 => PaginationHelper::getArgs() 사용
     *
     * @param \Ridibooks\Platform\Common\PagingUtil $pagingUtil
     * @param string $js_fn_name paging을 할 때 호출할 javascript함수명
     * @return string htmlTag
     */
    public static function getPagingTagByPagingDtoNew($pagingUtil, $js_fn_name = "fn_search")
    {
        $html = '';
        if ($pagingUtil->total > 0) {
            $html = '<ul class="pagination">';

            if ($pagingUtil->cpage > $pagingUtil->list_per_page) {
                $html .= '<li><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . 1 . ');">&laquo;</a></li>';
                $html .= '<li><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->prev_page_group . ');">&lsaquo;</a></li>';
            } else {
                $html .= '<li class="disabled"><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . 1 . ');">&laquo;</a></li>';
                $html .= '<li class="disabled"><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->prev_page_group . ');">&lsaquo;</a></li>';
            }
            for ($i = $pagingUtil->start_page; $i <= $pagingUtil->end_page; $i++) {
                if ($i == $pagingUtil->cpage) {
                    $html .= '<li class="active"><a href="javascript:void(0)">' . $i . '</a></li>';
                } else {
                    $html .= '<li><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $i . ');">' . $i . '</a></li>';
                }
            }

            if ($pagingUtil->num_page_group < $pagingUtil->last_page_group) {
                $html .= '<li><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->next_page_group . ');">&rsaquo;</a></li>';
                $html .= '<li><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->total_page . ');">&raquo;</a></li>';
            } else {
                $html .= '<li class="disabled"><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->next_page_group . ');">&rsaquo;</a></li>';
                $html .= '<li class="disabled"><a href="javascript:void(0)" onClick="' . $js_fn_name . '(' . $pagingUtil->total_page . ');">&raquo;</a></li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }
}
