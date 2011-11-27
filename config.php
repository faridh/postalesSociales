<?php


    function load_configuration()
    {
        
        switch ($_SERVER['HTTP_HOST'])
        {
            case 'postalitas.local':
                define('FACEBOOK_ID', '281719041871297');
                define('FACEBOOK_APP_SECRET', '165c732aec721e9ea5358c1f8c117e39');
                define('CANVAS_PAGE', 'https://apps.facebook.com/postalitas/');
                define('CANVAS_URL', 'postalitas.local/');
                define('FRONTEND_ENVIRONMENT', 'DEVELOPMENT');
            break;
          
            default:
                define('FACEBOOK_ID', getenv('FACEBOOK_APP_ID'));
                define('FACEBOOK_APP_SECRET', getenv('FACEBOOK_SECRET'));
                define('CANVAS_PAGE','https://apps.facebook.com/tarejtasnavidenas/');
                define('CANVAS_URL', 'https://freezing-rain-3994.herokuapp.com/');
                define('FRONTEND_ENVIRONMENT', 'PRODUCTION');
            break;
        }
        
    }
    
    load_configuration();
    
    function serverRequest($action, $parameters)
    {
        $requestObject                      = array();
        $requestObject['action']            = $action;
        $requestObject['actionParameters']  = $parameters;
        
        $object         = array();
        $object['body'] = json_encode($requestObject);

        $ch = curl_init(CANVAS_URL."index.php/main/".$action);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $object);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;
    }
?>
