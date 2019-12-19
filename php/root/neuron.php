<?php
    /**************************************************************************
    * neuron.php - Neuron Kernel and Launcher                                 *
    * ----------------------------------------------------------------------- *
    * Version 1.0, Revision 0, Concept Release                                *
    * Written by Timothy McClatchey <timothy@telosotech.com>                  *
    * Provided at no cost by Teloso Technologies <www.telosotech.com>         *
    * ----------------------------------------------------------------------- *
    * The purpose of this file is to provide a standalone kernel for neuron-  *
    * related web applications. The use of this kernel allows the application *
    * to mark the application as Neuron Driven, while using the whole Neuron  *
    * engine allows the application to be marked as Neuron Powered.           *
    * ----------------------------------------------------------------------- *
    * LICENSE OVERVIEW                                                        *
    **************************************************************************/

    /**
     * The Neuron Kernel allows for the most common of functionality to be 
     * shared between any Neuron Driven web application. This also allows for
     * a standardized execution path for all associated applications
     * 
     * @author Timothy McClatchey
     */
    abstract class Neuron
    {

        #region " Class Properties "

        /**
         * The executing version of the Neuron Kernel
         */
        public const VERSION = 1;

        /**
         * @var bool Is debugging enabled
         */
        private static $debuggingEnabled;

        /**
         * @var bool Has the Initialize() method been called
         */
        private static $hasInitialized;

        /**
         * @var bool Has the Startup() method been called
         */
        private static $hasStarted;

        /**
         * @var array[] Virtual path mappings
         */
        private static $mountPoints;

        /**
         * @var array[] Resources that have been loaded
         */
        private static $loadedResources;

        /**
         * @var array[] Drivers found within loaded resources
         */
        private static $driverList;

        /**
         * @var array[] Startup files found within loaded resources
         */
        private static $startupList;

        /**
         * @var array[] Methods registered for dynamic modification
         */
        private static $methodRegisters;

        /**
         * @var array[] Event hooks that have been defined and registered
         */
        private static $hookRegisters;

        /**
         * @var array[] Contents of the debugging log
         */
        private static $debuggingLog;

        /**
         * @var array[] View details that have been prepared to be sent
         */
        private static $preparedResult;

        #endregion

        #region " Initialization "
        
        /**
         * Begin initialization of the Neuron Kernel. The initialization flag
         * is checked at the beginning of every method call and if this method
         * has yet to be run it will be run.
         *
         * @return void
         */
        private static function Initialize()
        {
            // If this class has already been initialized, exit the routine
            if (Neuron::$hasInitialized === true) { return; }
            
            // Immediately flag that initialization has occur - we'll deal with errors later
            Neuron::$hasInitialized = true;

            // Set the default property values
            Neuron::$hasStarted = false;
            Neuron::$mountPoints = array();
            Neuron::$loadedResources = array('library' => array(), 'repository' => array()); // These are the default resource types
            Neuron::$driverList = array();
            Neuron::$startupList = array();
            Neuron::$methodRegisters = array();
            Neuron::$hookRegisters = array();
            Neuron::$debuggingLog = array();
            Neuron::$preparedResult = array();

            // Debugging is to be disabled by default for Production services
            Neuron::DisableDebugging();

            // Mount the / virtual path to the location of the kernel
            Neuron::Mount('/', dirname(__file__));

            // Mount the /public virtual path to the location of the entry point (index.php)
            if (DIRECTORY_SEPARATOR == '\\')
            {
                // This is a Windows-based system
                Neuron::Mount('/public', str_replace('/', '\\', dirname($_SERVER['SCRIPT_FILENAME'])));
            }
            else
            {
                // This is pretty much anything else
                Neuron::Mount('/public', dirname($_SERVER['SCRIPT_FILENAME']));
            }

            // Register all built-in event hooks
            Neuron::RegisterHook('nkernel-startup', array('post'));
            Neuron::RegisterHook('nkernel-present');
            Neuron::RegisterHook('nkernel-debug');
            Neuron::RegisterHook('nkernel-stop');
            Neuron::RegisterHook('nkernel-run', array('initialize', 'startup', 'process', 'shutdown', 'finalize'));
        }

        #endregion

        #region " Virtual File System "
        
        /**
         * Mount a virtual path to a real location for VFS
         *
         * @param string $virtualPath The virtual path to be mounted
         * @param string $realPath The real location of the target folder
         *
         * @return void
         */
        public static function Mount($virtualPath, $realPath)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // Ensure that the real path is absolute and not relative
            $realPath = realpath($realPath);

            // The virtual file system is not case sensitive
            $virtualPath = strtolower(trim($virtualPath));

            // Check if the real path exists and is not actually a file
            if (!(file_exists($realPath) && is_dir($realPath)))
            {
                Neuron::Kill('MOUNT', 'REALPATH_NOT_VALID', $realPath);
            }

            // Check if the virtual path has already been mounted
            if (array_key_exists($virtualPath, Neuron::$mountPoints))
            {
                Neuron::Kill('MOUNT', 'VIRTPATH_ALREADY_MOUNTED', $virtualPath);
            }

            // If the real path does not end with a directory separator, add it
            if (substr($realPath, strlen($realPath) - strlen(DIRECTORY_SEPARATOR)) != DIRECTORY_SEPARATOR)
            {
                $realPath.= DIRECTORY_SEPARATOR;
            }
            
            // If the virtual path does not end with a directory separator, add it.
            if (substr($virtualPath, strlen($virtualPath) - 1) != '/')
            {
                $virtualPath.= '/';
            }
            
            // Add the mount point
            Neuron::$mountPoints[$virtualPath] = $realPath;
        }

        /**
         * Convert a virtual path to a real path
         *
         * @param string $virtualPath This is the virtual path of the file or
         * folder that is trying to be converted to a real path.
         * @param bool $nullOnMissing If the file or folder cannot be found a
         * null value will be returned
         *
         * @return string
         */
        public static function FindPath($virtualPath, $nullOnMissing = true)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // The virtual file system is not case sensitive
            $virtualPath = strtolower(trim($virtualPath));

            // If the virtual path directly matches a mount point, return that mount point
            if (array_key_exists($virtualPath, Neuron::$mountPoints))
            {
                return Neuron::$mountPoints[$virtualPath];
            }

            // If the virtual path is missing the final directory separator, 
            // but matches a mount point, return that mount point
            if (array_key_exists($virtualPath.'/', Neuron::$mountPoints))
            {
                return Neuron::$mountPoints[$virtualPath.'/'];
            }

            // Get a list of virtual paths, then sort
            $virtualPathBases = array_keys(Neuron::$mountPoints);
            arsort($virtualPathBases);
            
            // Iterate through all virtual path bases
            foreach ($virtualPathBases as $vpb)
            {
                // Check if the virtual path starts with the current item
                if (substr($virtualPath, 0, strlen($vpb)) == $vpb)
                {
                    // Get the rest of the pathname after the virtual starting path
                    $fileName = substr($virtualPath, strlen($vpb));

                    // If this is not a Windows system, fix the directory separator
                    if (DIRECTORY_SEPARATOR != '/')
                    {
                        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
                    }

                    // Put path together, check and return result
                    $fileName = Neuron::$mountPoints[$vpb] . $fileName;
                    if ($nullOnMissing && !file_exists($fileName))
                    {
                        return null; // This file was not found AND a missing check was enabled
                    }
                    return $fileName; // Return the real file path
                }
            }

            // No potential real path could be found
            return false;
        }

        /**
         * Convert a virtual file path to a real file path
         *
         * @param string $virtualPath This is the virtual path of the file 
         * that is trying to be converted to a real path.
         * @param bool $nullOnMissing If the file cannot be found a null value
         * will be returned
         *
         * @return string
         */
        public static function FindFile($virtualFile, $nullOnMissing = true)
        {
            // Determine the 
            $realFile = Neuron::FindPath($virtualFile, $nullOnMissing === true);

            // Check the result and return the appropriate  infromation
            if ($realFile === false || $realFile === null) { return $realFile; }
            if ($nullOnMissing == true && !is_file($fileName))
            {
                return null;
            }
            return $realFile;
        }

        /**
         * Convert a virtual folder path to a real folder path
         *
         * @param string $virtualPath This is the virtual path of the folder 
         * that is trying to be converted to a real path.
         * @param bool $nullOnMissing If the folder cannot be found a null 
         * value will be returned
         *
         * @return string
         */
        public static function FindFolder($virtualFolder, $nullOnMissing = true)
        {
            $realFile = Neuron::FindPath($virtualFile, $nullOnMissing === true);
            if ($realFile == null) { return null; }
            if ($nullOnMissing == true && !is_dir($fileName))
            {
                return null;
            }
            return $realFile;
        }

        #endregion

        #region " Resource Management "
        
        /**
         * Load a library resource into the kernel to be utilized at startup
         * This method can only be called if startup has not been called.
         *
         * @param string $libraryName The name of the library to be loaded
         *
         * @return bool
         */
        public static function LoadLibrary($libraryName)
        {
            return Neuron::LoadResource('library', $libraryName, Neuron::FindPath('/library/'.$libraryName));
        }

        /**
         * Load a repository resource into the kernel to be utilized at startup
         * This method can only be called if startup has not been called.
         *
         * @param string $respositoryName The name of the repository to be loaded
         *
         * @return bool
         */
        public static function LoadRepository($respositoryName)
        {
            return Neuron::LoadResource('repository', $respositoryName, Neuron::FindPath('/repositories/'.$respositoryName));
        }

        /**
         * Load a resource into the kernel for access to startup modifiers, 
         * build resources, business logic and data access. Essentially acts as
         * a "plugin" or "module" would in most other data management systems.
         * This method can only be called if startup has not been called.
         *
         * @param string $type The type of resource being loaded
         * @param string $resourceName The name of the resource
         * @param string $resourcePath The realpath of the resource
         *
         * @return bool
         */
        private static function LoadResource($type, $resourceName, $resourcePath)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            if (Neuron::$hasStarted === true)
            {
                Neuron::Kill(strtoupper(substr($type, 0, 3)).'LOAD', 'STARTUP_ALREADY_CALLED');
            }
            if (!(file_exists($resourcePath) && is_dir($resourcePath)))
            {
                Neuron::Kill(strtoupper(substr($type, 0, 3)).'LOAD', 'RESOURCE_MISSING', $type, $resourceName);
            }
            if (substr($resourcePath, strlen($resourcePath) - strlen(DIRECTORY_SEPARATOR)) != DIRECTORY_SEPARATOR)
            {
                $resourcePath.= DIRECTORY_SEPARATOR;
            }

            if (!(file_exists($resourcePath . 'resource.dat') && is_file($resourcePath . 'resource.dat')))
            {
                Neuron::Kill(strtoupper(substr($type, 0, 3)).'LOAD', 'RESOURCE_INVALID', $type, $resourceName);
            }
            
            $resourceData = file($resourcePath . 'resource.dat');

            if ($resourceData[0] > Neuron::VERSION)
            {
                Neuron::Kill(strtoupper(substr($type, 0, 3)).'LOAD', 'RESOURCE_VERSION_UNSUPPORTED', $type, $resourceName, $resourceData[2]);
            }

            Neuron::$loadedResources[$type][] = $resourcePath;

            $startupFiles = array_values(array_diff(glob($resourcePath . 'startup' . DIRECTORY_SEPARATOR . '*.php'), Neuron::$startupList));
            Neuron::$startupList = array_merge(Neuron::$startupList, $startupFiles);

            if ($type == 'library')
            {
                $driverFiles = glob($resourcePath . 'drivers' . DIRECTORY_SEPARATOR . '*.php');
                foreach ($driverFiles as $driver)
                {
                    $driverName = basename(strtolower($driver), '.php');
                    if (array_key_exists($driverName, Neuron::$driverList))
                    {
                        trigger_error('DUPLICATE_DRIVER:'.$driverName, E_USER_WARNING);
                    }
                    else
                    {
                        Neuron::$driverList[$driverName] = array(false, $driver, array());
                    }
                }
            }
            return true;
        }

        public static function ExportResourcePaths($type)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            return Neuron::$loadedResources[$type];
        }

        /**
         * Enable a specific driver from a loaded resource
         *
         * @param string $driverName The name of the driver to enable
         *
         * @return void
         */
        public static function EnableDriver($driverName)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $driverName = strtolower(trim($driverName));
            if (!array_key_exists($driverName, Neuron::$driverList))
            {
                Neuron::Kill('DRVEN', 'DRIVER_NOT_INSTALLED', $driverName);
            }
            Neuron::$driverList[$driverName][0] = true;
        }

        #endregion

        #region " Method Management "
        
        /**
         * Register a dynamic method for callback
         *
         * @param string $methodName The name of the method callback
         * @param callback $methodCallback The callback to trigger when the method is called
         *
         * @return void
         */
        public static function RegisterMethod($methodName, $methodCallback)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $methodName = trim(strtolower($methodName));
            if (strlen($methodName) == 0)
            {
                Neuron::Kill('METHODREG', 'METHOD_NAME_EMPTY');
            }
            if (array_key_exists($methodName, Neuron::$methodRegisters))
            {
                Neuron::Kill('METHODREG', 'METHOD_ALREADY_REGISTERED', $methodName);
            }
            if (!is_callable($methodCallback))
            {
                Neuron::Kill('METHODREG', 'METHOD_NOT_CALLABLE', $methodName);
            }
            Neuron::$methodRegisters[$methodName] = $methodCallback;
        }

        /**
         * Trigger dynamic method
         *
         * @param string $methodName The name of the method to trigger
         *
         * @return mixed
         */
        public static function TriggerMethod($methodName)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $methodName = trim(strtolower($methodName));
            if (strlen($methodName) == 0)
            {
                Neuron::Kill('METHODCALL', 'METHOD_NAME_EMPTY');
            }
            if (!array_key_exists($methodName, Neuron::$methodRegisters))
            {
                Neuron::Kill('METHODCALL', 'METHOD_NOT_REGISTERED', $methodName);
            }
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array(Neuron::$methodRegisters[$methodName], $args);
        }

        /**
         * Alias for TriggerMethod
         *
         * @param string $methodName
         * @param array $args
         *
         * @return mixed
         */
        public static function __callstatic($methodName, $args)
        {
            $args = array_merge(array($methodName), $args);

            return call_user_func_array(array('Neuron', 'TriggerMethod'), $args);
        }

        /**
         * Check if a method has been registered
         *
         * @param string $methodName The name of the method to check
         *
         * @return bool
         */
        public static function IsMethodRegistered($methodName)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $methodName = trim(strtolower($methodName));
            if (strlen($methodName) == 0)
            {
                Neuron::Kill('METHODFIND', 'METHOD_NAME_EMPTY');
            }
            if (array_key_exists($methodName, Neuron::$methodRegisters))
            {
                return true;
            }
            return false;
        }

        #endregion

        #region " Hook Management "
        
        /**
         * Register an event hook
         *
         * @param string $hookName The name of the event hook to register
         * @param string[] $hookPositions different positions provided by the event hook that allows the event to be called at different points
         *
         * @return void
         */
        public static function RegisterHook($hookName, array $hookPositions = array('pre', 'post'))
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $hookName = trim(strtolower($hookName));
            if (strlen($hookName) == 0)
            {
                Neuron::Kill('HOOKREG', 'HOOK_NAME_EMPTY');
            }
            if (array_key_exists($hookName, Neuron::$hookRegisters))
            {
                Neuron::Kill('HOOKREG', 'HOOK_ALREADY_REGISTERED', $hookName);
            }
            Neuron::$hookRegisters[$hookName] = array();
            foreach ($hookPositions as $position)
            {
                $positionName = strtolower(trim($position));
                if (strlen($positionName) > 0 && !array_key_exists($positionName, Neuron::$hookRegisters[$hookName]))
                {
                    Neuron::$hookRegisters[$hookName][$positionName] = array();
                }
            }
            if (count(Neuron::$hookRegisters[$hookName]) == 0)
            {
                unset(Neuron::$hookRegisters[$hookName]);
                Neuron::Kill('HOOKREG', 'NO_POSITIONS_DEFINED', $hookName);
            }
        }

        /**
         * Bind an event hook to a method / calls the method when the event is fired
         *
         * @param string $hookName The name of the hook to tie to
         * @param callback $methodCallback What method to call when the event is fired
         * @param string $hookPosition What position the method should be assigned to
         *
         * @return void
         */
        public static function BindHook($hookName, $methodCallback, $hookPosition = 'post')
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $hookName = trim(strtolower($hookName));
            $hookPosition = trim(strtolower($hookPosition));
            if (strlen($hookName) == 0)
            {
                Neuron::Kill('HOOKBIND', 'HOOK_NAME_EMPTY');
            }
            if (strlen($hookPosition) == 0)
            {
                Neuron::Kill('HOOKBIND', 'HOOK_POSITION_EMPTY');
            }
            if (!array_key_exists($hookName, Neuron::$hookRegisters))
            {
                Neuron::Kill('HOOKBIND', 'HOOK_NOT_REGISTERED', $hookName);
            }
            if (!array_key_exists($hookPosition, Neuron::$hookRegisters[$hookName]))
            {
                Neuron::Kill('HOOKBIND', 'HOOK_POSITION_NOT_DEFINED', $hookName);
            }
            if (!is_callable($methodCallback))
            {
                Neuron::Kill('HOOKBIND', 'HOOK_NOT_CALLABLE', $hookName);
            }
            Neuron::$hookRegisters[$hookName][$hookPosition][] = $methodCallback;
        }

        /**
         * Fire an event hook calling all associated bindings
         *
         * @param string $hookName The name of the event hook to trigger
         * @param string $hookPosition Which position of the event hook to trigger
         *
         * @return mixed
         */
        public static function TriggerHook($hookName, $hookPosition = 'post')
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $hookName = trim(strtolower($hookName));
            $hookPosition = trim(strtolower($hookPosition));
            if (strlen($hookName) == 0)
            {
                Neuron::Kill('HOOKRUN', 'HOOK_NAME_EMPTY');
            }
            if (strlen($hookPosition) == 0)
            {
                Neuron::Kill('HOOKRUN', 'HOOK_POSITION_EMPTY');
            }
            if (!array_key_exists($hookName, Neuron::$hookRegisters))
            {
                Neuron::Kill('HOOKRUN', 'HOOK_NOT_REGISTERED', $hookName);
            }
            if (!array_key_exists($hookPosition, Neuron::$hookRegisters[$hookName]))
            {
                Neuron::Kill('HOOKRUN', 'HOOK_POSITION_NOT_DEFINED', $hookName);
            }
            $args = func_get_args();
            array_shift($args);
            array_shift($args);
            $result = array();
            foreach (Neuron::$hookRegisters[$hookName][$hookPosition] as $callback)
            {
                $result[] = call_user_func_array($callback, $args);
            }
            return $result;
        }

        /**
         * Check if a hook has been registered
         *
         * @param string $hookName The name of the hook being checked
         *
         * @return bool
         */
        public static function IsHookRegistered($hookName)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $hookName = trim(strtolower($hookName));
            if (strlen($hookName) == 0)
            {
                Neuron::Kill('HOOKFIND', 'HOOK_NAME_EMPTY');
            }
            if (array_key_exists($hookName, Neuron::$hookRegisters))
            {
                return true;
            }
            return false;
        }

        /**
         * Check if a hook has a specific position in its' definition list
         *
         * @param string $hookName Name of the hook that the position check is for
         * @param string $hookPosition The name of the position to check for
         *
         * @return bool
         */
        public static function IsHookPositionDefined($hookName, $hookPosition)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $hookName = trim(strtolower($hookName));
            $hookPosition = trim(strtolower($hookPosition));
            if (strlen($hookName) == 0)
            {
                Neuron::Kill('HOOKFIND', 'HOOK_NAME_EMPTY');
            }
            if (strlen($hookPosition) == 0)
            {
                Neuron::Kill('HOOKFIND', 'HOOK_POSITION_EMPTY');
            }
            if (!array_key_exists($hookName, Neuron::$hookRegisters))
            {
                Neuron::Kill('HOOKRUN', 'HOOK_NOT_REGISTERED', $hookName);
            }
            if (array_key_exists($hookPosition, Neuron::$hookRegisters[$hookName]))
            {
                return true;
            }
            return false;
        }

        #endregion

        #region " Kernel Startup "
        
        /**
         * Run all startup processings including loading associates startup files
         * and initiating drivers
         *
         * @return void
         */
        public static function Startup()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            
            foreach (Neuron::$startupList as $startupFile)
            {
                require_once($startupFile);
            }
            foreach (Neuron::$driverList as $driver => $data)
            {
                if ($data[0] === true)
                {
                    $classes = get_declared_classes();
                    require_once($data[1]);
                    $classes = array_diff(get_declared_classes(), $classes);
                    if (count($classes) > 0)
                    {
                        foreach ($classes as $class)
                        {
                            if (method_exists($class, 'InstanceOf'))
                            {
                                Neuron::$driverList[$driver][2][] = $class::InstanceOf();
                            }
                            else
                            {
                                Neuron::$driverList[$driver][2][] = new $class();
                            }
                        }
                    }
                }
            }

            Neuron::TriggerHook('nkernel-startup', 'post');

            Neuron::$hasStarted = true;
        }

        #endregion

        #region " Kernel Execution "

        /**
         * Run the application
         * 
         * Hook Name: nkernel-run
         * Hook Positions: initialize, startup, process, shutdown, finalize
         *
         * @return void
         */
        public static function Run()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            Neuron::TriggerHook('nkernel-run', 'initialize');
            Neuron::TriggerHook('nkernel-run', 'startup');
            Neuron::TriggerHook('nkernel-run', 'process');
            Neuron::TriggerHook('nkernel-run', 'shutdown');
            Neuron::TriggerHook('nkernel-run', 'finalize');
        }

        #endregion

        #region " View Management "

        /**
         * Prepare a view for presentation by adding it to the prepared result list.
         *
         * @param string $viewName The name of the view to be presented
         * @param array $viewParameters The parameters the view will be provided with
         *
         * @return void
         */
        public static function Prepare($viewName, array $viewParameters)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            Neuron::$preparedResult[] = array('view' => $viewName, 'parameters' => $viewParameters);
        }

        /**
         * Send the presentation to the application layer via ncore-encoded
         * json values. Once the data is presented, dump the result 
         * 
         * Hook Name: nkernel-present
         * Hook Positions: pre, post
         *
         * @param string $outputName The name of the output to render
         *
         * @return void
         */
        public static function Present($outputName)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $rogueContent = ob_get_clean();
            $lockedKeys = array('version', 'output', 'views');
            $result = array();
            foreach ($lockedKeys as $key)
            {
                $result[$key] = null;
            }
            $pre = Neuron::TriggerHook('nkernel-present', 'pre', $outputName);
            foreach ($pre as $item)
            {
                foreach ($item as $key => $value)
                {
                    $keyName = strtolower(trim($key));
                    if (!array_key_exists($keyName, $result))
                    {
                        $result[$keyName] = $value;
                    }
                }
            }
            $result['version'] = Neuron::VERSION;
            $result['views'] = Neuron::$preparedResult;
            $result['output'] = $outputName;
            if (Neuron::IsDebuggingEnabled())
            {
                $result['rogue'] = $rogueContent;
                $result['debug'] = Neuron::ExportDebuggingLog();
            }
            $post = Neuron::TriggerHook('nkernel-present', 'post', $outputName);
            foreach ($post as $item)
            {
                foreach ($item as $key => $value)
                {
                    $keyName = strtolower(trim($key));
                    if (!in_array($keyName, $lockedKeys))
                    {
                        if (!array_key_exists($keyName, $result))
                        {
                            $result[$keyName] = $value;
                        }
                        else
                        {
                            if (is_array($result[$keyName]) && is_array($value))
                            {
                                $result[$keyName] = array_merge($result[$keyName], $value);
                            }
                            else if (!is_array($result[$keyName]) && !is_array($value))
                            {
                                $result[$keyName].= $value;
                            }
                        }
                    }
                }
            }
            die('ncode:'.json_encode($result));
        }

        #endregion

        #region " Debugging "
        
        /**
         * Check to see if debugging has been enabled
         *
         * @return bool
         */
        public static function IsDebuggingEnabled()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // Return whether or not debugging is turned on
            return Neuron::$debuggingEnabled === true;
        }

        /**
         * Turn debugging mode on
         *
         * @return void
         */
        public static function EnableDebugging()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // Turn on debugging
            Neuron::$debuggingEnabled = true;
        }

        /**
         * Turn debugging mode off
         *
         * @return void
         */
        public static function DisableDebugging()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // Turn off debugging
            Neuron::$debuggingEnabled = false;
        }

        /**
         * Export the private debugging log variable for access and manipulation.
         *
         * @return array
         */
        public static function ExportDebuggingLog()
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }

            // Return the debugging log
            return Neuron::$debuggingLog;
        }

        /**
         * Generate a debugging message and save to the log
         * 
         * Hook Name: nkernel-debug
         * Hook Positions: pre, post
         *
         * @param string $message The message to send to the log
         *
         * @return void
         */
        public static function Debug($message)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $stamp = time();
            $datetime = date('m/d/Y H:i:s', $stamp);
            $pre = Neuron::TriggerHook('nkernel-debug', 'pre', $message, $stamp, $datetime);
            $logData = '['.$datetime.']';
            foreach ($pre as $item)
            {
                if (is_string($item))
                {
                    $logData.= ' '.$item;
                }
            }
            $logData.= ' '.$message;
            $post = Neuron::TriggerHook('nkernel-debug', 'post', $message, $stamp, $datetime);
            foreach ($post as $item)
            {
                if (is_string($item))
                {
                    $logData.= ' '.$item;
                }
            }
            Neuron::$debuggingLog[] = $logData;
        }

        /**
         * Generate a formatted debugging backtrace
         *
         * @param integer $skip The number of traces to skip
         * @param array $trace If available, the trace to use to process
         *
         * @return array
         */
        public static function Trace($skip = 0, $trace = null)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            $result = array();
            if ($trace == null)
            {
                $trace = debug_backtrace();
                if (count($trace) > 1)
                {
                    // Skip the call to Neuron::Trace unless it is the only call in the trace
                    $skip++;
                }
            }
            $traceIndex = 0;
            if ($skip < count($trace))
            {
                if (is_numeric($skip) && $skip > 0)
                {
                    $trace = array_slice($trace, $skip);
                }
                $trace = array_reverse($trace);
                for ($traceIndex = 0; $traceIndex < count($trace); $traceIndex++)
                {
                    $line = '#'.number_format($traceIndex, 0).' ';
                    if (array_key_exists('class', $trace[$traceIndex]))
                    {
                        $line.= $trace[$traceIndex]['class'].$trace[$traceIndex]['type'];
                    }
                    $line.= $trace[$traceIndex]['function'].'(';
                    if (count($trace[$traceIndex]['args']) > 0 && defined('DEBUGGING_MODE') && DEBUGGING_MODE === true)
                    {
                        for ($argIndex = 0; $argIndex < count($trace[$traceIndex]['args']); $argIndex++)
                        {
                            if (is_array($trace[$traceIndex]['args'][$argIndex]))
                            {
                                $line.= '{array('.count($trace[$traceIndex]['args'][$argIndex]).')}';
                            }
                            else if (is_string($trace[$traceIndex]['args'][$argIndex]))
                            {
                                $line.= '"'.addslashes($trace[$traceIndex]['args'][$argIndex]).'"';
                            }
                            else if (is_object($trace[$traceIndex]['args'][$argIndex]))
                            {
                                $line.= '{'.get_class($trace[$traceIndex]['args'][$argIndex]).'}';
                            }
                            else
                            {
                                $line.= $trace[$traceIndex]['args'][$argIndex];
                            }
                            if ($argIndex < count($trace[$traceIndex]['args']) - 1)
                            {
                                $line.= ', ';
                            }
                        }
                    }
                    $line.= ') called at [';
                    if (array_key_exists('file', $trace[$traceIndex]))
                    {
                        $line.= $trace[$traceIndex]['file'].':'.$trace[$traceIndex]['line'];
                    }
                    $line.= ']';
                    $result[] = $line;
                }
            }
            return $result;
        }

        #endregion

        #region " Crash Management "

        /**
         * Stop all processing and generate a formatted error presentation
         * 
         * Hook Name: nkernel-stop
         * Hook Positions: pre, post
         *
         * @param integer $code The unique error code number between 0 and 4,294,967,295
         * @param string $title The title of the error
         * @param string $message Detailed message on why the error occured
         * @param array $trace The backtrace to use
         * @param string $file The file the error occurred in
         * @param string $line The line number the error occurred on
         *
         * @return void
         */
        public static function Stop($code, $title, $message, $trace = null, $file = null, $line = null)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            
            if ($trace === null)
            {
                $trace = Neuron::Trace(1);
            }

            if (!is_numeric($code))
            {
                Neuron::Kill('STOP', 'CODE_NOT_NUMERIC', implode('|', $trace));
            }
            if ($code < 0)
            {
                Neuron::Kill('STOP', 'CODE_UNDERFLOW', implode('|', $trace));
            }
            if ($code > 4294967295)
            {
                Neuron::Kill('STOP', 'CODE_OVERFLOW', implode('|', $trace));
            }
            if (count($trace) > 0)
            {
                $lastTrace = $trace[count($trace) - 1];
            }
            if (count($trace) > 0 && strlen($file) == 0)
            {
                $openBracket = strpos($lastTrace, '[') + 1;
                $closeBracket = strrpos($lastTrace, ':') - $openBracket;
                $file = substr($lastTrace, $openBracket, $closeBracket);
            }
            if (count($trace) > 0 && strlen($line) == 0)
            {
                $openBracket = strrpos($lastTrace, ':') + 1;
                $closeBracket = strrpos($lastTrace, ']') - $openBracket;
                $line = substr($lastTrace, $openBracket, $closeBracket);
            }
            if ($file != null)
            {
                $location = $file;
                if ($line != null)
                {
                    $location.= ':'.$line;
                }
            }

            $pre = Neuron::TriggerHook('nkernel-stop', 'pre', $code, $title, $message, $trace, $file, $line);
            foreach ($pre as $item)
            {
                if (is_string($item))
                {
                    $parameters['details'][] = $item;
                }
            }

            $code = dechex($code);
            if (strlen($code) < 8)
            {
                $code = str_pad($code, 8, '0', STR_PAD_LEFT);
            }

            $parameters = array(
                'code' => $code,
                'title' => $title,
                'message' => $message,
                'trace' => $trace,
                'debug' => Neuron::ExportDebuggingLog(),
                'location' => $file . ':' . $line,
                'details' => array(),
                'raw' => null
            );

            $post = Neuron::TriggerHook('nkernel-stop', 'post', $code, $title, $message, $trace, $file, $line);
            foreach ($post as $item)
            {
                if (is_string($item))
                {
                    $parameters['details'][] = $item;
                }
            }

            $parameters['raw'] = ob_get_clean();

            Neuron::$preparedResult = array();
            Neuron::Prepare('stop', $parameters);
            Neuron::Present('crash');
        }

        /**
         * Kill processing for the engine and spit out a basic error code. To
         * be used when a non-recoverable error occurs and may not be at a
         * STOPable position yet
         * 
         * Any parameters after $code are passed as part of the error message
         *
         * @param string $phase The phase the operation is running in, such as a method or code chunk
         * @param string $code The error code that had occurred
         *
         * @return void
         */
        public static function Kill($phase, $code)
        {
            // Check if initialization has occurred and, if not, initialize.
            if (Neuron::$hasInitialized !== true) { Neuron::Initialize(); }
            ob_end_clean();
            $msg = 'NKILL-'.strtoupper($phase).':'.strtoupper($code);
            $trace = debug_backtrace();
            if (count($trace) > 0)
            {
                $trace = $trace[1];
                $msg.= ' ['.$trace['file'].':'.$trace['line'].']';
            }
            $argc = func_num_args() - 2;
            if ($argc > 0)
            {
                $argv = func_get_args();
                array_shift($argv);
                array_shift($argv);
                for ($i = 0; $i < $argc; $i++)
                {
                    $msg.= "\n--> ".$argv[$i];
                }
            }
            die($msg);
        }

        #endregion

        /**
         * Would normally create a new instance of this object but Neuron is
         * supposed to be treated as a static object so this function is never
         * called. Therefore, it is set as a Private methon, only callable from
         * within, but never is.
         *
         * @return void
         */
        private function __construct() { }
    }
?>