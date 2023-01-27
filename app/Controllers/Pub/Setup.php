<?php

namespace App\Controllers\pub;
use CodeIgniter\Controller;
$client = \Config\Services::curlrequest();
class Setup extends Controller {

    protected $helpers = ["url", "form"];
    protected $session;
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
        $this->session->start();
    }
    public function index() {
        $sess_id = $this->session->has('userid');
        $user_id = $this->session->has('login_userid');
        if (!empty($sess_id) || !empty($user_id)) {
            echo view('Views/aut/setup_view');
        } else {
            $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            return redirect()->to(base_url() . 'pub/start/signup?redirect_url=' . $redirect_url);
        }
    }
}
