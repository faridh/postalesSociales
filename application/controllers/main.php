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
        $friends        = json_decode($_POST['friends']);
        $title          = $_POST['title'];
        $message        = $_POST['message'];
        $backgroundId   = $_POST['backgroundId'];
        $songId         = $_POST['songId'];
                
        foreach ( $friends->friends as $friend )
        {
            $newPostcard    = array(
                'user_id'       => $userId,
                'background_id' => $backgroundId,
                'title'         => $title,
                'message'       => $message,
                'song_id'       => $songId,
                'friend_id'     => $friend->id,
                'created_at'    => time()
            );

            $this->db->insert('sent_postcards', $newPostcard);
        }
        
        die("{success:true}");
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
