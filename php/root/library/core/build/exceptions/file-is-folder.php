<?php
    class FileIsFolderException extends ExceptionBase
    {
        public function __construct($virtualFile)
        {
            parent::__construct('A data file was expected but a folder was found: {0}', $virtualFile);
        }
    }
?>