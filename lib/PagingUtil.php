<?php

namespace Ridibooks\Platform\Common;

class PagingUtil
{
	private $default_list_per_page = 10;
	private $default_line_per_page = 10;

	public $list_per_page;
	public $line_per_page;
	public $num_page_group;
	public $last_page_group;
	public $start_page;
	public $end_page;

	public $next_page_group;
	public $prev_page_group;

	public $total;
	public $total_page;
	public $cpage;
	public $start;
	public $limit;

	/**
	 * paging bean 생성자
	 * @param int $total
	 * @param int $cur_page
	 * @param int $listPerPage
	 * @param int $linePerPage
	 */
	public function __construct($total, $cur_page, $listPerPage = null, $linePerPage = null)
	{
		$this->list_per_page = $listPerPage ? $listPerPage : $this->default_list_per_page;
		$this->line_per_page = $linePerPage ? $linePerPage : $this->default_line_per_page;
		$this->total = $total;
		$this->cpage = $cur_page;
		$this->paging();
	}

	private function paging()
	{
		$this->cpage = $this->cpage < 1 ? 1 : $this->cpage;

		$this->total_page = ceil((double)$this->total / (double)$this->line_per_page);
		$this->total_page = $this->total_page < 1 ? 1 : $this->total_page;
		$this->cpage = $this->cpage > $this->total_page ? $this->total_page : $this->cpage;

		$this->num_page_group = (int)ceil((double)$this->cpage / $this->list_per_page);
		$this->last_page_group = (int)ceil((double)$this->total_page / $this->list_per_page);

		$this->start_page = ($this->num_page_group - 1) * $this->list_per_page + 1;
		$this->start_page = $this->start_page < 1 ? 1 : $this->start_page;

		$this->end_page = $this->start_page + $this->list_per_page - 1;
		$this->end_page = $this->end_page < 1 ? 1 : $this->end_page;
		$this->end_page = $this->end_page > $this->total_page ? $this->total_page : $this->end_page;

		$this->next_page_group = $this->end_page == $this->total_page ? $this->end_page : $this->end_page + 1;
		$this->prev_page_group = $this->start_page == 1 ? 1 : $this->start_page - 1;

		if ($this->cpage == 1) {
			$this->start = 0;
		} else {
			$this->start = ($this->cpage - 1) * $this->line_per_page;
		}

		$this->limit = $this->line_per_page;
	}
}
