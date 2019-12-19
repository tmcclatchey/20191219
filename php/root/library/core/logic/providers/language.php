<?php
    final class LanguageProvider extends ProviderBase
    {
        private static $isInitialized;
        private static $loadedSets;

        private static function Initialize()
        {
            LanguageProvider::$isInitialized = true;
            LanguageProvider::$loadedSets = array();
        }

        public static function __callstatic($setName, $args)
        {
            if (!LanguageProvider::$isInitialized)
            {
                LanguageProvider::Initialize();
            }

            $setName = trim($setName);

            if (!array_key_exists($setName, LanguageProvider::$loadedSets))
            {
                if (preg_match('/[a-zA-Z0-9\_]+/', $setName) != 1)
                {
                    throw new ArgumentValidationFailedException('setName', 'LanguageProvider::__callstatic', 'The group name must be provided and may only be letters, numbers and underscore.');
                }

                $namePieces = preg_split('/(?=[A-Z])/',$setName);
                $namePieces = array_filter($namePieces);
                $namePieces = array_map('strtolower', $namePieces);
                $namePieces = array_values($namePieces);

                $setPath = Neuron::FindFile('/data/language/'.implode('/', $namePieces))
            }

            if (count($args) == 0)
            {
                throw new ArgumentValidationFailedException('args[0]', 'LanguageProvider::__callstatic', 'A set item must be declared');
            }

            $setItem = array_shift($args);
            $setItem = trim(strtoupper($setItem));

            if (preg_match('/[A-Z0-9\_\-]+/', $setItem) != 1)
            {
                throw new ArgumentValidationFailedException('setItem', 'LanguageProvider::__callstatic', 'The group name must be provided and may only be letters, numbers, underscore, and hyphen.');
            }
        }

        private function __construct() { }
    }
?>