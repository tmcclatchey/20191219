<?php
    include('../root/neuron.php');

    Neuron::EnableDebugging();

    Neuron::Mount('/repositories/', '..\\repositories\\');

    Neuron::LoadLibrary('core');
    Neuron::LoadLibrary('raden');
    Neuron::LoadLibrary('rare');
    Neuron::LoadLibrary('sap');

    Neuron::LoadRepository('example-com');

    Neuron::EnableDriver('crash');
    Neuron::EnableDriver('loader-engine');
    Neuron::EnableDriver('data-manager');

    Neuron::Startup();

    //$test = LanguageCollection::Read('test');
    //$test->item1 /= 'blah1';
    var_dump(LanguageProvider::__callstatic('\\', ''));
    //var_dump(Neuron::GetData('language', 'test.json'));
    //$test = (new ConfigurationCollection('database'));
    //$test = ConfigurationCollection::Decode('test');
    //var_dump(($test));
    //var_dump($test->Encode());
    die();

    Neuron::Run();

    Neuron::Present('standard');
?>