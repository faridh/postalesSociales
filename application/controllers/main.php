<?php

require 'lib/facebook.php';

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Main extends CI_Controller 
{

    function Main() 
    {
        parent::__construct();

        $this->load->library('AlebrijeConfig');
        $this->alebrijeconfig->load_configuration();
    }

    public function index() 
    {
        $this->load->view('index');
    }

    public function registerUser() 
    {

        $this->load->database();

        //check if we already have registered the user
        $query = $this->db->get_where('users', array('id' => $_POST['data']['me']['id']), 1, 0);

        $total_likes = 0;

        if ($query->num_rows() == 0) 
        {
            //insert user
            $newUser = array(
                'id' => $_POST['data']['me']['id'],
                'mail' => $_POST['data']['email'],
                'escuela' => $_POST['data']['school'],
                'ano' => $_POST['data']['grade'],
                'etc' => json_encode($_POST['data']['me'])
            );
            $this->db->insert('users', $newUser);

            /*
              ProcessLikes
              Right now we will stick to FB id's to see if it works
             */

            $interests = array('books', 'games', 'movies', 'music', 'television');

            foreach ($interests as $interest)
            {
                if (array_key_exists($interest, $_POST['data'])) 
                {
                    $this->processLikes($_POST['data'][$interest], $_POST['data']['me']['id']);
                    $total_likes++;
                }
            }
        }
        else 
        {
            // DO NOTHING
        }

        die("User Successfully registered");
    }

    public function getTopMatches()
    {

        $this->load->database();

        $result = array();
        $likes_array = array();
        $types_array = array();
        $interests = array('books', 'games', 'movies', 'music', 'television');
        $uid = $_POST['data']['me']['id'];

        foreach ($interests as $interest) {
            if (array_key_exists($interest, $_POST['data']))
            {
                foreach ($_POST['data'][$interest] as $id => $text)
                {
                    if (!array_key_exists($id, $likes_array))
                    {
                        array_push($likes_array, $id);
                        $types_array[$id] = $interest;
                    }
                }
            }
        }

        // GET ALL THE LIKES
        // http://www.iisretard.com/wp-content/plugins/rss-poster/cache/de5c6_funny-facebook-fails-some-people.jpeg

        $where = "SELECT * FROM likes WHERE ";

        foreach ($likes_array as $like)
        {
            $where .= " id = " . $like . " OR ";
        }

        $where = substr($where, 0, -3);
        $where .= ";";

        $query = '';
        if (sizeof($likes_array) > 0) 
        {
            $query = $this->db->query($where);
        }

        if ($query != '') 
        {
            
            foreach ($query->result() as $row) 
            {
                // Let's optimize this code so it can be less than n^2
                $pureRow = substr($row->data, 1, -1);

                foreach (explode(',', $pureRow) as $user) 
                {
                    //skip yourself
                    if ($user != $uid && $user != '100003148835318')
                    {
                        if (array_key_exists($user, $result))
                        {
                            $result[$user]['total_likes']++;

                            if (!array_key_exists($types_array[$row->id], $result[$user]['likes']))
                                $result[$user]['likes'][$types_array[$row->id]] = array();

                            array_push($result[$user]['likes'][$types_array[$row->id]], $row->id);
                        }
                        else
                        {
                            $result[$user]['total_likes'] = 1;
                            $result[$user]['likes'] = array();
                            $result[$user]['id'] = $user;

                            if (!array_key_exists($types_array[$row->id], $result[$user]['likes']))
                                $result[$user]['likes'][$types_array[$row->id]] = array();

                            array_push($result[$user]['likes'][$types_array[$row->id]], $row->id);
                        }
                    }
                }
            }
        }

        /*
         * TODO: SORT RESULTS BASED ON total_likes
         */

        if (AlebrijeConfig::get('IKI_ENVIRONMENT') == 'dev')
        {

            //echo json_encode($result);
            echo '{
                    "702152773":
                    {   
                        "total_likes":18,
                        "likes":
                        {
                            "games":["113880908622809"],
                            "movies":["35481394342","91290503700","56727800941","39644305296"],
                            "music":["5660597307","322926881876","116713035013014","8232689025","61680950315","109553692403966","69116329538","8811047260","10212595263","7677942180","12922850458"],
                            "television":["7807422276","14176232250"]
                        },
                        "id":702152773
                    },
                    "500535225":
                    {
                        "total_likes":12,
                        "likes":
                        {
                            "games":["113880908622809","106469429388757"],
                            "movies":["35481394342","91290503700","56727800941","39644305296","106602966061813","109480629101278","162629997121827","104054269631460","115393811836969","74089565764"]
                        },
                        "id":500535225
                    }
                }';
        }
        else
        {
            echo json_encode($result);
        }
    }

    public function processLikes($array, $uid)
    {
        
        $this->load->database();
        foreach ($array as $id => $name) 
        {
            $query = $this->db->get_where('likes', array('id' => $id), 1, 0);

            if ($query->num_rows() == 0) 
            {
                $this->db->insert('likes', array('id' => $id, 'name' => $name, 'data' => '[' . $uid . ']'));
            } 
            else 
            {

                //get the string
                $row = $query->result();
                $row = $row[0];

                //look for the user
                $json = json_decode($row->data);
                //user already exists, skip
                if (!in_array($uid, $json)) 
                {
                    //remove the last character
                    $row->data = substr($row->data, 0, -1);

                    //add the id
                    $row->data .= ',' . $uid . ']';
                    $data = array('id' => $id, 'data' => $row->data);

                    //update
                    $this->db->update('likes', $data, array('id' => $id));
                }
            }
        }
    }

    public function getUser() 
    {
        $this->load->database();

        $response = new stdClass();
        $requestParameters = $_POST;
        $userId = $requestParameters['userId'];

        $query = $this->db->get_where('users', array('id' => $userId), 1, 0);

        if ($query->num_rows() == 0) 
        {
            $response->registeredUser = false;
            $response->albumId = 0;
        } 
        else 
        {
            $response->registeredUser = true;
            foreach($query->result() as $row)
            {
                $response->albumId = $row->album_id;
            } 
        }

        exit(json_encode($response));
    }
    
    public function getTopPosts()
    {
        $this->load->database();
        
        $select     = "SELECT * FROM posts LIMIT 10;";
        $result     = $this->db->query($select);
        $response   = new stdClass();
        
        if ( $result )
        {
            foreach ( $result->result() as $row )
            {
                $id             = $row->id;
                $response->$id  = $row;
            }
        }
        else
        {
            $response->response = true;
        }
        
        exit(json_encode($response));
    }
    
    public function registerAppAlbum() 
    {
        $this->load->database();
        
        $requestParameters = $_POST;
        $userId     = $requestParameters['userId'];
        $albumId    = $requestParameters['albumId'];
        
        $data = array('album_id' => $albumId);
        
        $this->db->where('id', $userId);
        $this->db->update('users', $data);
        
        
        
        exit("{success:true}");
    }
    
    public function uploadPhoto()
    {
        
        log_message("error", "uploadPhoto()");
        log_message("error", print_r($_POST, true));
        log_message("error", print_r($_FILES['uploadedfile'], true));
        
        $facebook = new Facebook(array(  
            'appId'  => AlebrijeConfig::get('FB_ID'),
            'secret' => AlebrijeConfig::get('FB_SECRET'),  
            'fileUpload' => true,  
            'cookie' => true // enable optional cookie support  
        ));
        
        $facebook->setFileUploadSupport(true);
        $access_token = $facebook->getAccessToken();

        # File is relative to the PHP doc  
        $file = "@".$_FILES['uploadedfile']['tmp_name'];  

        $args = array(  
            'message' => '',
            "access_token" => $access_token,
            "image" => $file  
        );

        $data = $facebook->api('/10150471706578993/photos', 'post', $args);
        if ($data) echo("uploadPhoto() SUCCESS: ".print_r($data, true));
        
    }

    public function suggestSchool() 
    {
        $escuelas = array("Escuela 1", "Escuela 2", "Otra");

        $results = array();

        foreach ($escuelas as $escuela) 
        {
            if (stripos($escuela, $_POST['name']) !== false)
                array_push($results, $escuela);
        }

        echo '<ul>';
        if (count($results) == 0)
        {
            echo '<li class="suggestion_li" onClick="fill(\'\');">No se encontraron escuelas</li>';
        }
        else 
        {
            foreach ($results as $school)
                echo '<li class="suggestion_li" onClick="fill(\'' . $school . '\');">' . $school . '</li>';
        }
        echo '</ul>';
    }

    public function addFriend() 
    {
        echo "<script type='text/javascript'>window.close();</script>";
    }

}
