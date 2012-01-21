<?php
/**
 * Provides a paginator
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Paginator
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Provides a paginator
 *
 * @category   MicroFramework
 * @package    Paginator
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Paginator
{
    /**
     * @var int The total number of items
     */
    protected $itemCount;

    /**
     * @var int The number of items per page
     */
    protected $pageSize = 50;

    /**
     * @var int The number of items per page
     */
    protected $windowSize = 5;

    /**
     * @var int The current number
     */
    protected $currentPage;

    /**
     * @var int The total number of pages
     */
    protected $totalPages;

    /**
     * @var int The index of the first item on the current page
     */
    protected $firstIndex;

    /**
     * @var array The pages in the paginator
     */
    public $pages;

    /**
     * Creates instance of the paginator
     *
     * @param int $itemCount The total number of items
     * @param int $currentPage The current pagenumber
     * @param null|int $pageSize The number of items on the page
     * @param null|int $windowSize The number of visible pages in the paginator
     * @return void
     */
    function __construct($itemCount, $currentPage = 1, $pageSize = null, $windowSize = null)
    {
        $this->setItemCount($itemCount);

        $this->setCurrentPage = $currentPage;

        if ($pageSize !== null) {
            $this->setPageSize = $pageSize;
        }

        if ($windowSize !== null) {
            $this->windowSize = $windowSize;
        }

        $this->setTotalPages();

        $this->setFirstIndex();



        $this->pages = $this->get_paginator();
    }

    /**
     * Set the total amount of items
     *
     * @param int $itemCount The total number of items
     * @return void
     */
    protected function setItemCount($itemCount)
    {
        $this->itemCount = $itemCount;
    }

    /**
     * Get the total amount of items
     *
     * @return int The total number of items
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * Set the current page
     *
     * @param int $currentPage The current page
     * @return void
     */
    protected function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * Get the current page
     *
     * @return int The current page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the number of items per page
     *
     * @param int $pageSize The number of items per page
     * @return void
     */
    protected function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Get the number of items per page
     *
     * @return int The number of items per page
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Set the window size
     * If the window size is an odd number it will we incremented by 1 to be able to center the current page
     *
     * @param int $windowSize The number of pages displayed in the paginator
     * @return void
     */
    protected function setWindowSize($windowSize)
    {
        if ($windowSize % 2 == 0) {
            $this->windowSize = $windowSize + 1;
        } else {
            $this->windowSize = $windowSize;
        }
    }

    /**
     * Get the window size
     *
     * @return int The number of pages displayed in the paginator
     */
    public function getWindowSize()
    {
        return $this->windowSize;
    }

    /**
     * Set the total number of pages
     *
     * @return void
     */
    protected function setTotalPages()
    {
        $pages = ceil($this->itemCount() / $this->getPageSize());

        if ($pages > 0) {
            $this->totalPages = $pages;
        } else {
            $this->totalPages = 1;
        }
    }

    /**
     * Get the total number of pages
     *
     * @return int The total number of pages
     */
    public function getTotalPages()
    {
        return $this->totalPages();
    }

    /**
     * Set the first index of the current page
     *
     * @return void
     */
    protected function setFirstIndex()
    {
        $this->firstIndex = ($this->getCurrentPage() * $this->pageSize()) - $this->getPageSize();
    }

    /**
     * Get the first index of the current page
     *
     * @return int The first index of the page
     */
    public function getFirstIndex()
    {
        return $this->firstIndex;
    }

    /**
     * Set the first index of the current page
     *
     * @return void
     */
    protected function setPages()
    {
        $this->firstIndex = ($this->getCurrentPage() * $this->pageSize()) - $this->getPageSize();
    }

    /**
     * Sets all the pages of the paginator
     *
     * @return void
     */
    function setPages() {
        if ($this->getCurrentPage() > $this->getTotalPages()) {
            return array();
        }

        $pages = array();

        $firstPage = $this->getFirstVisiblePage();
        $lastPage = $this->getLastVisiblePage();

        if ($firstPage > 1) {
            $page =  new stdClass();
            $page->number = '…';
            $page->selected = false;
            $page->link = false;
            $pages[] = $page;
        }

        for ($i = $firstPage; $i <= $lastPage; $i++) {
            $page =  new stdClass();
            $page->number = $i;
            $page->selected = false;
            $page->link = true;

            if ($i == $this->getCurrentPage()) {
                $page->selected = True;
            }

            $pages[] = $page;
        }

        if ($lastPage < $this->getTotalPages()) {
            $page =  new stdClass();
            $page->number = '…';
            $page->selected = false;
            $page->link = false;
            $pages[] = $page;
        }

        $this->pages = $pages;
    }

    /**
     * Gets the first visible page in the paginator
     *
     * @return int The first visible page
     */
    protected function getFirstVisiblePage()
    {
        $visiblePages = $this->getNumberOfVisiblePages();

        if ($this->getTotalPages() <= $visiblePages) {
            return 1;
        }

        $firstPage = $this->getCurrentPage() - floor($this->getWindowSize() / 2);

        if ($firstPage < 1) {
            $firstPage = 1;
        }

        $lastPage = $firstPage + $visiblePages - 1;

        if ($lastPage > $this->getTotalPages()) {
            $lastPage = $this->getTotalPages();
            $firstPage = $lastPage - $visiblePages + 1;
        }

        return $firstPage;
    }

    /**
     * Gets the last visible page in the paginator
     *
     * @return int The last visible page
     */
    protected function getLastVisiblePage()
    {
        $visiblePages = $this->getNumberOfVisiblePages();

        if ($this->getTotalPages() > $visiblePages) {
            return $this->getTotalPages();
        }

        $firstPage = $this->getCurrentPage() - floor($this->getWindowSize() / 2);

        if ($firstPage < 1) {
            $firstPage = 1;
        }

        $lastPage = $firstPage + $visiblePages - 1;

        if ($lastPage > $this->getTotalPages()) {
            $lastPage = $this->getTotalPages();
        }

        return $lastPage;
    }

    /**
     * Gets the number of visible pages in the view window
     *
     * @return int The number of visible pages
     */
    protected function getNumberOfVisiblePages()
    {
        return floor($this->getWindowSize() / 2) * 2 + 1;
    }
}