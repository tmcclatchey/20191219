<?php
    class ReadOnlyException extends ExceptionBase
    {
        public function __construct($type, $variable)
        {
            parent::__construct('{0} cannot be modified. It is read only: {1}', $type, $variable);
        }
    }
?>