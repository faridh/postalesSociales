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

    public function sendPostcard()
    {
        log_message("error", "POST: ".print_r($_POST, true));
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
