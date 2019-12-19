<?php
    class DecodingFailedException extends ExceptionBase
    {
        public function __construct()
        {
            parent::__construct('Decoding failed. Check the JSON source.');
        }
    }
?>