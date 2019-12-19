<?php
    final class NeuronCoreCrashManagementDriver
    {
        public function __construct()
        {
            set_error_handler(array($this, 'handleError'));
            set_exception_handler(array($this, 'handleException'));
        }

        public function handleError($code, $message, $file, $line)
        {
            $title = '';
            switch ($code)
            {
                case E_ERROR:
                    $title = 'PHP Error';
                    break;
                case E_USER_ERROR:
                    $title = 'Application Error';
                    break;
                case E_WARNING:
                    Neuron::Debug('PHPWARN '.$message.' ['.$file.':'.$line.']');
                    return;
                case E_NOTICE:
                    Neuron::Debug('PHPNOTE '.$message.' ['.$file.':'.$line.']');
                    return;
                case E_USER_WARNING:
                    Neuron::Debug('APPWARN '.$message.' ['.$file.':'.$line.']');
                    return;
                case E_USER_NOTICE:
                    Neuron::Debug('APPNOTE '.$message.' ['.$file.':'.$line.']');
                    return;
                case E_DEPRECATED:
                    Neuron::Debug('PHPDEPR '.$message.' ['.$file.':'.$line.']');
                    return;
                case E_USER_DEPRECATED:
                    Neuron::Debug('APPDEPR '.$message.' ['.$file.':'.$line.']');
                    return;
            }
            $trace = Neuron::Trace(2);
            Neuron::Stop(42, $title, $message, $trace, $file, $line);
        }

        public function handleException($exception)
        {
            $traceskip = 0;
            if (property_exists($exception, 'traceskip') && is_numeric($exception->traceskip) && $exception->traceskip > 0)
            {
                $traceskip += $exception->traceskip;
            }
            $message = $exception->getMessage();
            Neuron::Stop(23, 'Unhandled Exception ('.get_class($exception).')', $message, Neuron::Trace($traceskip, $exception->getTrace()), $exception->getfile(), $exception->getLine());
        }
    }
?>