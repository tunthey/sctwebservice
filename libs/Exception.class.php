<?php

#for further information please check -> http://php.net/manual/en/language.exceptions.extending.php

interface IException{

    /* Protected methods inherited from Exception class */

    public function getMessage();                 // Exception message
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct($message = null, $code = 0);
}

abstract class AppException extends Exception implements IException{

    protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code    = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown

    public function __construct($message = null, $code = 0){
        if (!$message)
            throw new $this('Unknown '. get_class($this));
        parent::__construct($message, $code);
    }
    public function __toString(){
        return  get_class($this) . " '{$this->message}' in {$this->file}({$this->line})"
        . "{$this->getTraceAsString()}";
    }
}

/*
 * Some php errors cannot be handled with set_error_handler. E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
 * E_COMPILE_ERROR, E_COMPILE_WARNING, and most of E_STRICT raised in the file where set_error_handler() is called.
 * But anyway I wrote all (:
 * */
set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context){
    // error was suppressed with the @-operator
    if (0 === error_reporting())  return false;
    switch($err_severity)
    {
        case E_ERROR:               throw new ErrorException            ($err_msg, 0, $err_severity, $err_file, $err_line);
       // case E_WARNING:             throw new WarningException          ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_PARSE:               throw new ParseException            ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_NOTICE:              throw new NoticeException           ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_CORE_ERROR:          throw new CoreErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_CORE_WARNING:        throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_COMPILE_ERROR:       throw new CompileErrorException     ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_COMPILE_WARNING:     throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_USER_ERROR:          throw new UserErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_USER_WARNING:        throw new UserWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_USER_NOTICE:         throw new UserNoticeException       ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_STRICT:              throw new StrictException           ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_RECOVERABLE_ERROR:   throw new RecoverableErrorException ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_DEPRECATED:          throw new DeprecatedException       ($err_msg, 0, $err_severity, $err_file, $err_line);
        case E_USER_DEPRECATED:     throw new UserDeprecatedException   ($err_msg, 0, $err_severity, $err_file, $err_line);
    }
});

class DatabaseException             extends AppException {}
class WarningException              extends AppException {}
class ParseException                extends AppException {}
class NoticeException               extends AppException {}
class CoreErrorException            extends AppException {}
class CoreWarningException          extends AppException {}
class CompileErrorException         extends AppException {}
class CompileWarningException       extends AppException {}
class UserErrorException            extends AppException {}
class UserWarningException          extends AppException {}
class UserNoticeException           extends AppException {}
class StrictException               extends AppException {}
class RecoverableErrorException     extends AppException {}
class DeprecatedException           extends AppException {}
class UserDeprecatedException       extends AppException {}
class LoggerException               extends AppException {}