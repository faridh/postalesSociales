<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AlebrijeConfig 
{

    public static $alebrije_config;

    function AlebrijeConfig() 
    {
        
    }

    static function set($key, $value) 
    {
        self::$alebrije_config[$key] = $value;
    }

    static function get($key) 
    {
        return self::$alebrije_config[$key];
    }

    function load_configuration() 
    {
        log_message('debug', "Loading alebrije configuration from: " . print_r($_SERVER, true));

        //Environment
        if (substr_count($_SERVER['HTTP_HOST'], 'local') || substr_count($_SERVER['HTTP_HOST'], '192.168') || substr_count($_SERVER['HTTP_HOST'], '127.0.0.1')) 
        {
            $this->set('IKI_ENVIRONMENT', 'dev');
            $this->set('FB_ID', '163631993729394');
            $this->set('FB_SECRET', '1ef18e3338d0aad8b67da5f0823dc297');
        }

        if (substr_count($_SERVER['HTTP_HOST'], 'ikistudio.com')) 
        {
            $this->set('IKI_ENVIRONMENT', 'stage');
            $this->set('FB_ID', '236798669709731');
            $this->set('FB_SECRET', '64745b23ef3b4a16fe3ac1f772eeeba9');
        }

        if (substr_count($_SERVER['HTTP_HOST'], 'ikigaming.com')) 
        {
            $this->set('IKI_ENVIRONMENT', 'prod');
            $this->set('FB_ID', '');
            $this->set('FB_SECRET', '');
        }

        if (substr_count($_SERVER['HTTP_HOST'], 'ikimania.com')) 
        {
            $this->set('IKI_ENVIRONMENT', 'cloud');
            $this->set('FB_ID', '');
            $this->set('FB_SECRET', '');
        }

/*
        switch ($_SERVER['HTTP_HOST']) 
        {
            
            //development
            case 'amigositesm.local':
                define('ENVIRONMENT', 'DEV');
            break;
            
            //stage
            case 'amigositesm.ikistudio.com':
               define('ENVIRONMENT', 'STAGE');
            break;
            
            //production
            case 'amigositesm.ikigaming.com':
               define('ENVIRONMENT', 'PRODUCTION');
           break;
            
        }
 */  
     }

    function unload_configuration() 
    {
        
    }

}

?>
