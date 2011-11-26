<?php


    function load_configuration()
    {
        
        switch ($_SERVER['HTTP_HOST'])
        {
            case 'amigositesm.local':
                define('FACEBOOK_ID', '163631993729394');
                define('FACEBOOK_APP_SECRET', '1ef18e3338d0aad8b67da5f0823dc297');
                define('CANVAS_PAGE', 'https://apps.facebook.com/amigositesm_dev/');
                define('CANVAS_URL', 'amigositesm.local/');
                define('FRONTEND_ENVIRONMENT', 'DEVELOPMENT');
            break;
            
            case 'amigositesm-phoenix.local':
                define('FACEBOOK_ID', '287164581314174');
                define('FACEBOOK_APP_SECRET', '2df5123b5d8c267650be7e625f571de3');
                define('CANVAS_PAGE', 'https://apps.facebook.com/amigositesm_dev_phnx/');
                define('CANVAS_URL', 'amigositesm-phoenix.local/');
                define('FRONTEND_ENVIRONMENT', 'DEVELOPMENT');
            break;
        
            case 'amigositesm.ikistudio.com':
                define('FACEBOOK_ID', '236798669709731');
                define('FACEBOOK_APP_SECRET', '64745b23ef3b4a16fe3ac1f772eeeba9');
                define('CANVAS_PAGE', 'https://apps.facebook.com/amigositesm_stage/');
                define('CANVAS_URL', 'amigositesm.ikistudio.com/');
                define('FRONTEND_ENVIRONMENT', 'STAGE');
            break;
            
            case 'amigositesm.ikigaming.com':
                define('FACEBOOK_ID', '236798669709731');
                define('FACEBOOK_APP_SECRET', '64745b23ef3b4a16fe3ac1f772eeeba9');
                define('CANVAS_PAGE', 'https://apps.facebook.com/amigositesm_stage/');
                define('CANVAS_URL', 'amigositesm.ikigaming.com/');
                define('FRONTEND_ENVIRONMENT', 'PROD');
            break;
        
            default:
                define('FACEBOOK_ID', '215386991867071');
                define('FACEBOOK_APP_SECRET', '96fad92f86943cf56faa628a32f167cd');
                define('CANVAS_PAGE', 'http://apps.facebook.com/ikitest/');
                define('CANVAS_URL', 'http://127.0.0.1/test/');
                define('CANVAS_SECURE_URL', '');
                define('FRONTEND_ENVIRONMENT', 'DEVELOPMENT');
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
