<?php
require_once __DIR__ . "/../dao/ExamDao.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);



class ExamService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new ExamDao();
    }

    public function login($data) {
        $customer = $this->dao->login($data);

        //  No record returned
        if (!$customer) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        //  Create JWT payload
        $payload = [
            'id'    => $customer['customer_id'], // exam might be $user['id']
            'email' => $customer['email'],
            'exp'   => time() + 3600 // expires in 1h
        ];

        /* $secret = "your-secret-key"; // exam: can be any string
        $jwt = JWT::encode($payload, $secret, 'HS256'); */

        return [
            'success'  => true,
            'message'  => 'Login successful',
            /* 'token'    => $jwt, */
            'customer' => $customer
        ];
    }


    public function film_performance_report() {
        return $this->dao->film_performance_report();
    }

    public function delete_film($film_id) {
        return $this->dao->delete_film($film_id);
    }

    public function edit_film($film_id, $data) {
        return $this->dao->edit_film($film_id, $data);
    }

    public function get_customers_report() {
        return $this->dao->get_customers_report();


    }

    public function get_customer_rentals($customer_id) {
        return $this->dao->get_customer_rentals($customer_id);
    }
    
}
