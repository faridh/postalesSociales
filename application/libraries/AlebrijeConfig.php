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
        if (substr_count($_SERVER['HTTP_HOST'], 'local') || substr_count($_SERVER['HTTP_HOST'], '127.0.0.1')) 
        {
            $this->set('IKI_ENVIRONMENT', 'DEV');
            $this->set('FB_ID', '281719041871297');
            $this->set('FB_SECRET', '165c732aec721e9ea5358c1f8c117e39');
        }
        else
        {
            $this->set('IKI_ENVIRONMENT', 'PRODUCTION');
            $this->set('FB_ID', FACEBOOK_APP_ID);
            $this->set('FB_SECRET', FACEBOOK_SECRET); 
        }
        
     }

    function unload_configuration() 
    {
        
    }

}

?>
