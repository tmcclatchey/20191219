<?php
    abstract class ExceptionBase extends Exception
    {
        public int $traceskip = 1;
        public function __construct()
        {
            $arguments = func_get_args();
            $message = null;
            if (count($arguments) > 0)
            {
                $message = array_shift($arguments);
            }
            if (count($arguments) > 0)
            {
                $text = $message;
                for ($i = 0; $i < count($arguments); $i++)
                {
                    $text = str_replace('{'.$i.'}', (string)$arguments[$i], $text);
                }
                $message = $text;
            }
            if ($message !== null)
            {
                parent::__construct($message);
            }
        }
    }
?>