<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
/**
 * Постраничный вывод данных
 * @author r3c130n
 * 
 * @comment [pronix: 2011-11-04] Бесполезный класс, поскольку погинатор должен встравиваться в движок получения данных из БД
 */
class Paginator {
	public $currentPage;
	public $perPage;
	public $totalCount;

	public function __construct($page=1, $per_page=20, $total_count=0){
		$this->currentPage = (int) $page;
		$this->perPage = (int) $per_page;
		$this->totalCount = (int) $total_count;
	}
	
	public function Offset() {
		return ($this->currentPage - 1) * $this->perPage;
	}

	public function TotalPages() {
		return ceil($this->totalCount/$this->perPage);
	}

	public function PrevPage() {
		return $this->currentPage - 1;
	}

	public function NextPage() {
		return $this->currentPage + 1;
	}

	public function HasPrevPage() {
		return $this->PrevPage() >= 1 ? true : false;
	}

	public function HasNextPage() {
		return $this->NextPage() <= $this->TotalPages() ? true : false;
	}

	public function GetHtml() {
		$out = '<div class="page-navigation">';
		$total = $this->TotalPages();
		if ($total > 1) {
			if ($this->HasPrevPage()) {
				$out .=	' <a href="#URL#'.$this->PrevPage().'">←&nbsp;'.LANG('PAGINATOR_PREV').'</a> ';
			}

			for ($n=1; $n<=$total; $n++) {
				if ($n == $this->currentPage) {
					$out .=	' <a href="#URL#'.$n.'" class="active">'.$n.'</a> ';
				} else {
					$out .=	' <a href="#URL#'.$n.'">'.$n.'</a> ';
				}
			}

			if ($this->HasNextPage()) {
				$out .=	' <a href="#URL#'.$this->NextPage().'">'.LANG('PAGINATOR_NEXT').'&nbsp;→</a> ';
			}
		}
		$out .= '</div>';
		return $out;
	}
}

/******************************
 *			HOW TO:
 ******************************
 *
 * 1. Определим значения переменных:
 *  $page = 1; // Текущая страница
 *  $per_page = 5; // По сколько на страницу
 *  $total_count = 23; // Сколько всего
 *
 * 2. Создадим экземпляр объекта класса:
 *  $pagination = new Paginator($page, $per_page, $total_count);
 *
 * 3. Получим HTML код постраничного вывода информации
 *	$html = $pagination->GetHtml();
 *
 * 4. Используем в запросах:
 *  $sql = "SELECT * FROM `table_name` LIMIT {$per_page} OFFSET {$pagination->Offset()}";
 */
?>