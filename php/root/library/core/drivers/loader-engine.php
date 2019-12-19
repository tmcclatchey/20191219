<?php
    final class NeuronCoreLoaderEngineDriver
    {
        private $rules;
        public function __construct()
        {
            $this->rules = array();

            Neuron::RegisterMethod('RegisterAutoload', array($this, 'RegisterAutoload'));

            $this->RegisterAutoload('I([A-Z].*)', '/build/interfaces/', '-');
            $this->RegisterAutoload('E([A-Z].*)', '/build/enums/', '-');
            $this->RegisterAutoload('(.*)Base', '/build/', '-');
            $this->RegisterAutoload('(.*)Exception', '/build/exceptions/', '-');
            $this->RegisterAutoload('(.*)Collection', '/logic/collections/', '-');
            $this->RegisterAutoload('(.*)Helper', '/logic/helpers/', '-');
            $this->RegisterAutoload('(.*)Provider', '/logic/providers/', '-');
            $this->RegisterAutoload('(.*)Struct', '/logic/structs/', '-');

            spl_autoload_register(array($this, 'handleAutoload'));
        }

        public function RegisterAutoload($regex, $searchIn, $separator, array $resourceOrder = array('library', 'repository'))
        {
            $regex = trim($regex);
            if (preg_match('/^'.$regex.'$/', null) === false)
            {
                Neuron::Kill('CORELOADREG', 'RULE_NOT_VALID', $regex);
            }
            if (array_key_exists($regex, $this->rules))
            {
                Neuron::Kill('CORELOADREG', 'RULE_ALREADY_DEFINED', $regex);
            }
            $this->rules[$regex] = array($searchIn, $separator, $resourceOrder);
        }

        public function handleAutoload($className)
        {
            $classPieces = array();
            $separator = '-';
            $matches = array();
            $rule = false;
            foreach ($this->rules as $regex => $data)
            {
                if (preg_match_all('/^'.$regex.'$/', $className, $matches) > 0)
                {
                    $classPieces = preg_split('/(?=[A-Z])/',$matches[1][0]);
                    $classPieces = array_filter($classPieces);
                    $classPieces = array_map('strtolower', $classPieces);
                    $classPieces = array_values($classPieces);
                    $rule = $data;
                    break;
                }
            }
            if ($rule === false)
            {
                $classPieces = preg_split('/(?=[A-Z])/',$className);
                $classPieces = array_filter($classPieces);
                $classPieces = array_map('strtolower', $classPieces);
                $classPieces = array_values($classPieces);
                $rule = array('/logic/', '-', array('library', 'repository'));
            }
            if ($separator != $rule[1])
            {
                $separator = $rule[1];
            }

            if (substr($rule[0], 0, 1) == '/')
            {
                $rule[0] = substr($rule[0], 1);
            }
            if (strlen($rule[0]) > 0 && substr($rule[0], strlen($rule[0]) - 1) != '/')
            {
                $rule[0].= '/';
            }

            if (DIRECTORY_SEPARATOR != '/')
            {
                $rule[0] = str_replace('/', DIRECTORY_SEPARATOR, $rule[0]);
            }

            foreach ($rule[2] as $resourceType)
            {
                $paths = Neuron::ExportResourcePaths($resourceType);
                foreach ($paths as $path)
                {
                    $testPath = $path.$rule[0].implode($separator, $classPieces).'.php';
                    if (file_exists($testPath) && is_file($testPath))
                    {
                        require_once($testPath);
                        if (class_exists($className, false))
                        {
                            return;
                        }
                    }
                }
            }
        }
    }
?>