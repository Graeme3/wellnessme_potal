<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends ClientsController
{

    private $date;
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('clients_authentication_constructor', $this);
        $this->data = $this->retrieveJson();
        file_put_contents("data.log", print_r($this->data, true),true);
    }

    public function index()
    {
        $this->login();
    }


    // Added for backward compatibilies
    public function admin()
    {
        redirect(admin_url('authentication'));
    }

    public function login()
    {

        if (is_client_logged_in()) {
            redirect(site_url());
        }

        $this->form_validation->set_rules('password', _l('clients_login_password'), 'required');
        $this->form_validation->set_rules('email', _l('clients_login_email'), 'trim|required|valid_email');

        if (get_option('use_recaptcha_customers_area') == 1
            && get_option('recaptcha_secret_key') != ''
            && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }
        if ($this->form_validation->run() !== false) {
            $this->load->model('Authentication_model');

            $success = $this->Authentication_model->login(
                $this->input->post('email'),
                $this->input->post('password', false),
                $this->input->post('remember'),
                false
            );

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', _l('inactive_account'));
                redirect(site_url('authentication/login'));
            } elseif ($success == false) {
                set_alert('danger', _l('client_invalid_username_or_password'));
                redirect(site_url('authentication/login'));
            }

            $this->load->model('announcements_model');
            $this->announcements_model->set_announcements_as_read_except_last_one(get_contact_user_id());

            hooks()->do_action('after_contact_login');

            maybe_redirect_to_previous_url();
            redirect(site_url());
        }
        if (get_option('allow_registration') == 1) {
            $data['title'] = _l('clients_login_heading_register');
        } else {
            $data['title'] = _l('clients_login_heading_no_register');
        }
        $data['bodyclass'] = 'customers_login';

        $this->data($data);
        $this->view('login');
        $this->layout();
    }

    public function register()
    {
        if (get_option('allow_registration') != 1 || is_client_logged_in()) {
            redirect(site_url());
        }

        if (get_option('company_is_required') == 1) {
            $this->form_validation->set_rules('company', _l('client_company'), 'required');
        }

        if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions') == 1) {
            $this->form_validation->set_rules(
                'accept_terms_and_conditions',
                _l('terms_and_conditions'),
                'required',
                    ['required' => _l('terms_and_conditions_validation')]
            );
        }

        $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
        $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
        $this->form_validation->set_rules('email', _l('client_email'), 'trim|required|is_unique[' . db_prefix() . 'contacts.email]|valid_email');
        $this->form_validation->set_rules('password', _l('clients_register_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('clients_register_password_repeat'), 'required|matches[password]');

        if (get_option('use_recaptcha_customers_area') == 1
            && get_option('recaptcha_secret_key') != ''
            && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }

        $custom_fields = get_custom_fields('customers', [
            'show_on_client_portal' => 1,
            'required'              => 1,
        ]);

        $custom_fields_contacts = get_custom_fields('contacts', [
            'show_on_client_portal' => 1,
            'required'              => 1,
        ]);

        foreach ($custom_fields as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        foreach ($custom_fields_contacts as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                define('CONTACT_REGISTERING', true);

                $clientid = $this->clients_model->add([
                      'billing_street'      => $data['address'],
                      'billing_city'        => $data['city'],
                      'billing_state'       => $data['state'],
                      'billing_zip'         => $data['zip'],
                      'billing_country'     => is_numeric($data['country']) ? $data['country'] : 0,
                      'firstname'           => $data['firstname'],
                      'lastname'            => $data['lastname'],
                      'email'               => $data['email'],
                      'contact_phonenumber' => $data['contact_phonenumber'] ,
                      'website'             => $data['website'],
                      'title'               => $data['title'],
                      'password'            => $data['passwordr'],
                      'company'             => $data['company'],
                      'vat'                 => isset($data['vat']) ? $data['vat'] : '',
                      'phonenumber'         => $data['phonenumber'],
                      'country'             => $data['country'],
                      'city'                => $data['city'],
                      'address'             => $data['address'],
                      'zip'                 => $data['zip'],
                      'state'               => $data['state'],
                      'custom_fields'       => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
                ], true);

                if ($clientid) {
                    hooks()->do_action('after_client_register', $clientid);

                    if (get_option('customers_register_require_confirmation') == '1') {
                        send_customer_registered_email_to_administrators($clientid);

                        $this->clients_model->require_confirmation($clientid);
                        set_alert('success', _l('customer_register_account_confirmation_approval_notice'));
                        redirect(site_url('authentication/login'));
                    }

                    $this->load->model('authentication_model');

                    $logged_in = $this->authentication_model->login(
                        $this->input->post('email'),
                        $this->input->post('password', false),
                        false,
                        false
                    );

                    $redUrl = site_url();

                    if ($logged_in) {
                        hooks()->do_action('after_client_register_logged_in', $clientid);
                        set_alert('success', _l('clients_successfully_registered'));
                    } else {
                        set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                        $redUrl = site_url('authentication/login');
                    }

                    send_customer_registered_email_to_administrators($clientid);
                    redirect($redUrl);
                }
            }
        }

        $data['title']     = _l('clients_register_heading');
        $data['bodyclass'] = 'register';
        $this->data($data);
        $this->view('register');
        $this->layout();
    }

    public function forgot_password()
    {
        if (is_client_logged_in()) {
            redirect(site_url());
        }

        $this->form_validation->set_rules(
            'email',
            _l('customer_forgot_password_email'),
            'trim|required|valid_email|callback_contact_email_exists'
        );

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $this->load->model('Authentication_model');
                $success = $this->Authentication_model->forgot_password($this->input->post('email'));
                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('inactive_account'));
                } elseif ($success == true) {
                    set_alert('success', _l('check_email_for_resetting_password'));
                } else {
                    set_alert('danger', _l('error_setting_new_password_key'));
                }
                redirect(site_url('authentication/forgot_password'));
            }
        }
        $data['title'] = _l('customer_forgot_password');
        $this->data($data);
        $this->view('forgot_password');

        $this->layout();
    }

    public function reset_password($staff, $userid, $new_pass_key)
    {
        $this->load->model('Authentication_model');
        if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', _l('password_reset_key_expired'));
            redirect(site_url('authentication/login'));
        }

        $this->form_validation->set_rules('password', _l('customer_reset_password'), 'required');
        $this->form_validation->set_rules('passwordr', _l('customer_reset_password_repeat'), 'required|matches[password]');
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                hooks()->do_action('before_user_reset_password', [
                    'staff'  => $staff,
                    'userid' => $userid,
                ]);
                $success = $this->Authentication_model->reset_password(
                        0,
                        $userid,
                        $new_pass_key,
                        $this->input->post('passwordr', false)
                );
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', _l('password_reset_key_expired'));
                } elseif ($success == true) {
                    hooks()->do_action('after_user_reset_password', [
                        'staff'  => $staff,
                        'userid' => $userid,
                    ]);
                    set_alert('success', _l('password_reset_message'));
                } else {
                    set_alert('danger', _l('password_reset_message_fail'));
                }
                redirect(site_url('authentication/login'));
            }
        }
        $data['title'] = _l('admin_auth_reset_password_heading');
        $this->data($data);
        $this->view('reset_password');
        $this->layout();
    }

    public function logout()
    {
        $this->load->model('authentication_model');
        $this->authentication_model->logout(false);
        hooks()->do_action('after_client_logout');
        redirect(site_url('authentication/login'));
    }

    public function contact_email_exists($email = '')
    {
        $this->db->where('email', $email);
        $total_rows = $this->db->count_all_results(db_prefix() . 'contacts');

        if ($total_rows == 0) {
            $this->form_validation->set_message('contact_email_exists', _l('auth_reset_pass_email_not_found'));

            return false;
        }

        return true;
    }

    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }

    public function login_app()
    {

        $email    = $this->data["email"];
        $password = $this->data["password"];
        $remember = $this->data["remember"];
        // is_null($email)|| !preg_match("/^[a-zA-Z-' ]*$/",$email) || empty($email)
        if ( is_null($email)) {
            $this->buildPayload(false, "Not an email", [], ["error" => $email]);
            return;
        }

        if(count($password) > 6) {
            $this->buildPayload(false, "password length is less than 6", [], ["error"=> $password]);
            return;
        }


        $this->load->model('Authentication_model');
        try{
            $success = $this->Authentication_model->login(
                $email,
                $password,
                $remember,
                false,
                true
            );
            file_put_contents("successConrtoler.log", print_r([$success, "controller"],true));
            if(is_array($success)) {
                if($success['status'] === true) {
                    $this->buildPayload(true, "success", ['user' => $success['user'], "hit" => true], []);
                    return;
                }else {
                    $this->buildPayload(true, "success", ['user' => $success['error'], "hit" => true], []);
                    return;
                }
            } else if ($success  === false){
                $this->buildPayload(false, "failed to find user", [], ["error" => $email]);
                return;
            } else {
                $this->buildPayload(false, "failed to find user", [], ["error" => $success]);
                return;
            }
        }catch (Exception $e) {
            $this->buildPayload(false, "failed to find user", [], ["error" => $e]);
            return;
        }

    }

    public function register_app()
    {

        $firstname    = $this->data["firstname"];
        $lastname    = $this->data["lastname"];
        $phonenumber    = $this->data["phonenumber"];
        $email    = $this->data["email"];
        $password = $this->data["password"];
        $data = [];
        if(empty($firstname)) {
            $this->$this->buildPayload(false, "missing first name", [], ["error" => $firstname]);
            return false;
        }else {
            $data['firstname'] = $firstname;
        }

        if(empty($lastname)) {
            $this->$this->buildPayload(false, "missing last name", [], ["error" => $lastname]);
            return false;
        }else {
            $data['lastname'] = $lastname;
        }

        if(empty($phonenumber) && strlen($phonenumber) != 10) {
            $this->$this->buildPayload(false, "missing phone number or length is less or more than 10", [], ["error" => $phonenumber]);
            return false;
        }else {
            $data['phonenumber'] = $phonenumber;
        }

        if(empty($email)) {
            $this->$this->buildPayload(false, "missing email", [], ["error" => $email]);
            return false;
        } else {
            $data['email'] = $email;
        }

        if(empty($password)) {
            $this->$this->buildPayload(false, "missing password", [], ["error" => $password]);
            return false;
        }else {
            $data['password'] = app_hash_password($password);
        }
        $data['datecreated'] = date("Y-m-d H:i:s");
        $this->load->model("Contracts_model");


        try{
            $success = $this->Contracts_model->saveRegister($data);
            //file_put_contents("insert_id.log", print_r($success, true));
            if(is_array($success)) {
                if($success['status'] === true) {
                    $this->buildPayload(true, "success", ['user_id' => $success['data'], "hit" => true], []);
                    return;
                }else {
                    $this->buildPayload(true, "success", ['user_id' => $success['data'], "hit" => true], []);
                    return;
                }
            } else if ($success === false){
                $this->buildPayload(false, "failed to find user", [], ["error" => $success]);
                return;
            } else {
                $this->buildPayload(false, "failed to find user", [], ["error" => $success]);
                return;
            }
        }catch(Exception $e) {
            $this->buildPayload(false, "Error saving user", [], ["error" => $e]);
            return;
        }
    }

    public function forgot_password_app()
    {
        $email    = $this->data["email"];
        if ( is_null($email)) {
            $this->buildPayload(false, "Not an email", [], ["error" => $email]);
            return;
        }

            try{
                $this->load->model('Authentication_model');
                $success = $this->Authentication_model->forgot_password($email);
                if($success) {
                    $this->buildPayload(true, "success", ['user' => $success['user'], "hit" => true], []);
                    return;
                }
                else {
                    $this->buildPayload(false, "failed to find user", [], ["error" => $success]);
                    return;
                }
            }catch(Exception $e) {
                $this->buildPayload(false, "failed to find user", [], ["error" => $e]);
                return;
            }

    }

    /*public function get_all_products()
    {
        $this->load->model("Contracts_model");
        $success =
    }*/

    private function retrieveJson()
    {
        $json = file_get_contents('php://input');
        file_put_contents("json.log", print_r($json,true),true);
        $data = [];
        try{
            $data  = json_decode($json,true);

        }catch (Exception $e) {
            file_put_contents("jsonEX.log", print_r($e,true),true);
            return;
        }
        return $data;
    }

    private function echoJson($data)
    {
        echo json_encode($data);
        return;
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
