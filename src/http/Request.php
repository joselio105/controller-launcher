<?php

namespace Plugse\Ctrl\http;

/**
 * Define os parâmetros recebidos por uma requisição HTTP
 */
class Request
{
    private $queryString;
    public $controller;
    public $method;
    public $params;
    public $id;
    public $pagination;
    public $page;
    public $pageLimit;
    public $order;
    public $orderDescending;
    public $body;
    public $header;

    public function __construct()
    {
        $this->setQueryString();
        $this->setMethod();
        $this->setController();
        $this->setId();
        $this->setPagination();
        $this->setPage();
        $this->setPageLimit();
        $this->setOrder();
        $this->setOrderDescending();
        $this->setParams();
        $this->setBody();
        $this->setHeader();
    }

    private function setQueryString()
    {
        $query = key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : '';
        $this->queryString = explode('&', $query);
    }

    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    private function setController()
    {
        $this->controller = strlen($this->queryString[0]) > 0 ? $this->queryString[0] : 'index';
        unset($this->queryString[0]);
    }

    private function setParams()
    {
        $this->params = [];

        foreach ($this->queryString as $clause) {
            $parts = explode('=', $clause);
            if (key_exists(1, $parts)) {
                $this->params[$parts[0]] = $parts[1];
            }
        }
    }

    private function setId()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/id\=*(.*)/', $clause, $matches);
            if (!empty($matches)) {
                $this->id = $matches[1];
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->id = false;
    }

    private function setPagination()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/pagination\=*(.*)/', $clause, $matches);
            if (!empty($matches)) {
                $this->pagination = true;
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->pagination = false;
    }

    private function setPage()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/page\=*(.*)/', $clause, $matches);
            if (!empty($matches)) {
                $this->page = $matches[1];
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->page = 1;
    }

    private function setPageLimit()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/pageLimit\=*(.*)/', $clause, $matches);
            if (!empty($matches)) {
                $this->pageLimit = $matches[1];
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->pageLimit = 12;
    }

    private function setOrder()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/order\=(.+)/', $clause, $matches);
            if (!empty($matches)) {
                $this->order = $matches[1];
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->order = 'id';
    }

    private function setOrderDescending()
    {
        foreach ($this->queryString as $key => $clause) {
            preg_match('/orderDescending\=*(.*)/', $clause, $matches);
            if (!empty($matches)) {
                $this->orderDescending = true;
                unset($this->queryString[$key]);

                return;
            }
        }

        $this->orderDescending = false;
    }

    private function setBody()
    {
        $this->body = $this->method === 'POST'
            ? $this->getPostBody()
            : $this->getDefaultBody();
    }

    private function setHeader()
    {
        $this->header = function_exists('apache_request_headers') ? apache_request_headers() : [];
    }

    private function getPostBody()
    {
        $input = $_POST;
        foreach ($_FILES as $key => $value) {
            $input[$key] = $value;
        }

        return $input;
    }

    private function getDefaultBody()
    {
        $input = file_get_contents('php://input');

        return (array)json_decode($input);
    }
}
