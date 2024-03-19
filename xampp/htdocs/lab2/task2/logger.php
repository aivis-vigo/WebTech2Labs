<?php

class Logger
{
    #ip and GET parameters cannot change during execution
    private $ip;
    private $queryString;
    #this will be passed to constructor
    private $logPath;

    public function __construct($logPath)
    {
        $this->ip = $_SERVER["REMOTE_ADDR"] ?: "Failed";
        $this->queryString = $_SERVER["QUERY_STRING"] ?: "manufacturer=&country=&year=";
        $this->logPath = $logPath;
    }

    protected function generateLine($result)
    {
        $date = date('c', time());
        return sprintf("[%s][%s][%s][%s]\n", $this->ip, $date, $this->queryString, $result);
    }

    public function log($result)
    {
        file_put_contents($this->logPath, $this->generateLine($result), FILE_APPEND);
    }
}