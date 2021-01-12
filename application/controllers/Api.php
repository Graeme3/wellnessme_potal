<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends ClientsController
{
    private $date;
    public function __construct()
    {

        $this->load->model("Contracts_model");
        $this->data = $this->retrieveJson();
        file_put_contents("data.log", print_r($this->data),true);
    }

    public function login()
    {

        $email    = $this->data["email"];
        $password = $this->data["password"];
        $remember = $this->data["remember"];

        if (!preg_match("/^[a-zA-Z-' ]*$/",$email)) {
            $this->buildPayload(false, "Not an email", [], ["error" => $email]);
        }

        if(count($password) < 6) {
            $this->buildPayload(false, "password length is less than 6", [], ["error"=> $password]);
        }


            $this->load->model('Authentication_model');
            $success = $this->Authentication_model->login(
                $email,
                $password,
                $remember,
                false
            );
        if(is_array($success)) {
            if($success['status']) {
                $this->buildPayload(true, "success", ['user' => $success['user']]);
            }
        } else {
            $this->buildPayload(false, "failed to find user", [], ["error" => $email]);
        }
    }

    public function register()
    {

        $firstname    = $this->data["firstname"];
        $lastname    = $this->data["lastname"];
        $phonenumber    = $this->data["phonenumber"];
        $email    = $this->data["email"];
        $password = $this->data["password"];
        $data = [];
        if(empty($firstname)) {
            $this->$this->buildPayload(false, "missing first name", [], ["error" => $firstname]);
        }else {
            $data['firstname'] = $firstname;
        }

        if(empty($lastname)) {
            $this->$this->buildPayload(false, "missing last name", [], ["error" => $lastname]);
        }else {
            $data['lastname'] = $lastname;
        }

        if(empty($phonenumber)) {
            $this->$this->buildPayload(false, "missing phone number", [], ["error" => $phonenumber]);
        }else {
            $data['phonenumber'] = $phonenumber;
        }

        if(empty($email)) {
            $this->$this->buildPayload(false, "missing email", [], ["error" => $email]);
        } else {
            $data['email'] = $email;
        }

        if(empty($password)) {
            $this->$this->buildPayload(false, "missing password", [], ["error" => $password]);
        }else {
            $data['password'] = app_hash_password($password);
        }

        $this->load->model("Contracts_model");

        $sucuss = $this->Contracts_model->saveRegister($data);



    }

    private function retrieveJson()
    {
        $json = file_get_contents('php://input');
        file_put_contents("json.log", print_r($json),true);
        $data = [];
        try{
          $data  = json_decode($json,true);
        }catch (Exception $e) {
            file_put_contents("jsonEX.log", print_r($e),true);
        }
        return $data;
    }


    private function echoJson($data)
    {
        echo json_encode($data);
        retrun;
    }

    private function buildPayload($status, $message, $data, $error)
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'error'   => $error
        ];

        $this->echoJson($response);
    }

}