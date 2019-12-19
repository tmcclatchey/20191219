<?php
    class ArgumentValidationFailedException extends ExceptionBase
    {
        public function __construct($variable, $method, $reason)
        {
            parent::__construct('Validation for variable {0} on method {1} failed: {2}', $variable, $method, $reason);
        }
    }
?>