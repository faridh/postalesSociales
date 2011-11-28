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
    
    public function getUserSentPostcards()
    {
        $this->load->database();
        
        $userId                     = $_POST['userId'];
        $response                   = new stdClass();
        $response->userId           = $userId;
        $response->postcards        = array();
        
        $query = $this->db->get_where('sent_postcards', array('user_id' => $userId));
        
        foreach ($query->result() as $row) 
        {
            $id                         = $row->id;
            $response->postcards[$id]   = $row;
        }
        
        die(json_encode($response));
    }

    public function sendPostcard()
    {
        
        $this->load->database();
        
        $userId         = $_POST['userId'];
        $friends        = $_POST['friends'];
        $title          = $_POST['title'];
        $message        = $_POST['message'];
        $backgroundId   = $_POST['backgroundId'];
        $songId         = $_POST['songId'];
        
        log_message("error", "POST: ".print_r($_POST, true));
        
        foreach ( $friends['friends'] as $friend )
        {
            $newPostcard    = array(
                'user_id'       => $userId,
                'background_id' => $backgroundId,
                'title'         => $title,
                'message'       => $message,
                'song_id'       => $songId,
                'friend_id'     => $friend['id'],
                'created_at'    => time()
            );

            $this->db->insert('sent_postcards', $newPostcard);
        }
        
        die("{success:true}");
    }
   
    public function createDB()
    {
        $this->load->database();
        $this->db->query("CREATE TABLE sent_postcards (
  id bigint(20) unsigned NOT NULL ,
  user_id varchar(20) NOT NULL DEFAULT '',
  background_id varchar(10) NOT NULL DEFAULT '',
  song_id varchar(10) NOT NULL DEFAULT '',
  title varchar(50) NOT NULL DEFAULT '',
  message varchar(145) NOT NULL DEFAULT '',
  friend_id longtext NOT NULL,
  request_id varchar(20) NOT NULL DEFAULT '',
  created_at int(11) DEFAULT NULL,
  PRIMARY KEY (id)
            );");


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

}
