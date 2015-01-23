<?php

use LoggerException;

class Logger{

    protected $fileHandle = NULL;
    protected $timeFormat = 'd.m.Y - H:i:s';
    const FILE_CHMOD = 756;
    const NOTICE = '[NOTICE]';
    const WARNING = '[WARNING]';
    const ERROR = '[ERROR]';
    const FATAL = '[FATAL]';

    public function __construct($logfile) {
        if($this->fileHandle == NULL){
            $this->openLogFile($logfile);
        }
    }

    public function __destruct() {
        $this->closeLogFile();
    }

    public function log($message, $messageType = Logger::WARNING) {
        if($this->fileHandle == NULL){
            throw new LoggerException('Logfile is not opened.');
        }

        if(!is_string($message)){
            throw new LoggerException('$message is not a string');
        }

        if($messageType != Logger::NOTICE &&
            $messageType != Logger::WARNING &&
            $messageType != Logger::ERROR &&
            $messageType != Logger::FATAL
        ){
            throw new LoggerException('Wrong $messagetype given.');
        }

        $this->writeToLogFile("[".$this->getTime()."]".$messageType." - ".$message);
    }

    private function writeToLogFile($message) {
        flock($this->fileHandle, LOCK_EX);
        fwrite($this->fileHandle, $message.PHP_EOL);
        flock($this->fileHandle, LOCK_UN);
    }

    private function getTime() {
        return date($this->timeFormat);
    }

    protected function closeLogFile() {
        if($this->fileHandle != NULL) {
            fclose($this->fileHandle);
            $this->fileHandle = NULL;
        }
    }

    public function openLogFile($logFile) {
        $this->closeLogFile();

        if(!is_dir(dirname($logFile))){
            if(!mkdir(dirname($logFile), Logger::FILE_CHMOD, true)){
                throw new LoggerException('Could not find or create directory for log file.');
            }
        }

        if(!$this->fileHandle = fopen($logFile, 'a+')){
            throw new LoggerException('Could not open file handle.');
        }
    }

}