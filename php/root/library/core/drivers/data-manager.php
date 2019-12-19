<?php
    final class NeuronCoreDataManagerDriver
    {
        private static $instance;
        public static function InstanceOf()
        {
            if (!isset(NeuronCoreDataManagerDriver::$instance))
            {
                NeuronCoreDataManagerDriver::$instance = new NeuronCoreDataManagerDriver();
            }
            return NeuronCoreDataManagerDriver::$instance;
        }

        private function __construct()
        {
            Neuron::RegisterMethod('GetData', array($this, 'OpenDataFile'));
        }

        public function OpenDataFile($dataType, $virtualFile)
        {
            $virtualFile = '/data/'.$dataType.'/'.$virtualFile;
            $realFile = Neuron::FindPath($virtualFile, false);
            if (!file_exists($realFile))
            {
                $repos = Neuron::ExportResourcePaths('repository');
                foreach ($repos as $repo)
                {
                    $testFile = Neuron::FindPath('/repositories/'.basename($repo).$virtualFile);
                    if ($testFile !== null)
                    {
                        $realFile = $testFile;
                        break;
                    }
                }
            }
            if (!file_exists($realFile))
            {
                throw new FileNotFoundException($virtualFile);
            }
            if (is_dir($realFile))
            {
                throw new FileIsFolderException($virtualFile);
            }
            return array(
                $virtualFile,
                $realFile,
                file_get_contents($realFile)
            );
        }
    }
?>