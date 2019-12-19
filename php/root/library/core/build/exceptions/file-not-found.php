<?php
    class FileNotFoundException extends ExceptionBase
    {
        public function __construct($filename)
        {
            parent::__construct('File could not be found: {0}', $filename);
        }
    }
?>