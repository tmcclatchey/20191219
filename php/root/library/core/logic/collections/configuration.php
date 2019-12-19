<?php
    class ConfigurationCollection extends CollectionBase
    {
        private $name;
        private $virtualFile;
        private $realFile;
        private $fileType;
        public function __construct($configurationName)
        {
            parent::__construct();
            $this->name = $configurationName;
            $this->Reload();
        }

        public function getName()
        {
            return $this->name;
        }

        public function getVirtualFile()
        {
            return $this->virtualFile;
        }

        public function getRealFile()
        {
            return $this->realFile;
        }

        public function getFileType()
        {
            return $this->fileType;
        }

        public function Reload()
        {
        }
    }
?>