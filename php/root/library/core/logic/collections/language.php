<?php
    class LanguageCollection extends CollectionBase
    {
        private static $languageCode;
        private static $loadedConfigurations;
        public static function Read($setName)
        {
            if (!isset(LanguageCollection::$languageCode))
            {
                LanguageCollection::$languageCode = 'en-us';
            }
            if (!isset(LanguageCollection::$loadedConfigurations))
            {
                LanguageCollection::$loadedConfigurations = array();
            }
            if (array_key_exists($setName, LanguageCollection::$loadedConfigurations))
            {
                return LanguageCollection::$loadedConfigurations[$setName];
            }
            try {
                $data = NeuronCoreDataManagerDriver::InstanceOf()->
                    OpenDataFile('language', LanguageCollection::$languageCode . '/' . $setName . '.json');
            } catch (\Throwable $th) {
                return false;
            }
            var_dump($data);
        }
        private $name;
        private $virtualFile;
        private $realFile;
        private function __construct()
        {
            parent::__construct();
            /*$this->name = $configurationName;
            $data = NeuronCoreDataManagerDriver::InstanceOf()->OpenDataFile('language', $configurationName . '.json');
            $this->virtualFile = $data[0];
            $this->realFile = $data[1];
            $this->data = json_decode($data[2], true);
            if ($this->data === null)
            {
                throw new DecodingFailedException();
            }*/
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
    }
?>