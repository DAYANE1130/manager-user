<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\JwtService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Exception;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

use CodeIgniter\Cookie\Cookie;
use DateTime;
use DateTimeZone;

class Auth extends BaseController
{

    use ResponseTrait; // PARA TRATAMENTO DE ERROS PARA API
    protected $userModel = 'App\Models\UserModel';
    protected $format    = 'json';
    public function __construct()
    {
        // $this->session = \Config\Services::session();
    }

    public function index()
    {
        // $data = [];
        // if (session()->has('errors')) {
        //     $data['errors'] = session('errors');
        // }
        return view('formLogin');
    }

    private function generateJwt($userId, $email)
    {

        // Gera o token JWT , COLOCAR EM UM HELPER/ UTILS
        $key = getenv('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600 * 24;  // Token válido por 24 horas
        $payload = [
            'iss' => 'theissuer',
            'aud' => 'theaudience',
            'iat' => $iat,
            'exp' => $exp,
            'userId' => $userId,
            'email' => $email

        ];

        // Codifica o token JWT, gera token
        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
    }

    //
    public function register()
    {
        $authService = service('auth');
        // Verifica se a requisição é GET para renderizar a view
        if ($this->request->getMethod() === 'GET') {
            return view('formRegister');  // Renderiza a view formregister.php
        }

        // Caso contrário, trata como POST (processa o cadastro)

        $userModel = new UserModel();

        // Definindo o contexto de validação para "register"
        $userModel->setValidationContext('register');


        // Dados do usuário a serem salvos
        $userData = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'profile'  => $this->request->getPost('profile')
        ];

        // //VALIDANDO DADOS ENVIADOS NA REQUISIÇÃO
        if (!$userModel->validate($userData)) {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }

        // SALVANDO user NO BANCO DE DADOS
        $userModel->save($userData);

        // Se salvar com sucesso
        // PEGA O ID DO USUARIO CRIADO
        $userId = $userModel->getInsertId();

        $token = $this->generateJwt($userId, $userData['email']);
        var_dump($token);

        // Salvando user na session
        $this->session->set('loggedUser', $userData);
        return redirect()->to('/site')->with('success', 'Cadastro concluído com sucesso!');
    }


    // Login do usuário
    public function login()
    {
        // Verifica se a requisição é GET para renderizar a view
        if ($this->request->getMethod() === 'GET') {
            return view('formLogin');  // Renderiza a view register.php
        }
        // INSTANCIA A MODEL
        $userModel = model('App\Models\UserModel');

        // Definindo o contexto de validação para "register"
        $userModel->setValidationContext('login');

        // PEGA DADOS DA REQUISIÇÃO
        $userData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        if (!$userModel->validate($userData)) {

            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }

        // Inicializa a variável $rememberMe com false
        $rememberMe = $this->request->getPost('remember_me') ? true : false; // Verifica se o checkbox está marcado

        // BUSCA USUÁRIO COM ESSE E-MAIL NO BANCO  
        $user = $userModel->where('email', $userData['email'])->first();
        // dd(password_verify($password, $user['password']));



        if (!$user || !password_verify($userData['password'], $user['password'])) {
            // Cria um array de mesmo nome e insere  erro manualmente
            $errors = ['login' => 'Email ou senha inválidos'];
            return redirect()->back()->withInput()->with('errors', $errors);
        }


        // Gerar token JWT
        //FAZER DEPOIS A FUNÇÃO DE VALIDAR O TOKEN  (isso dará autorização a rotas necessárias)
        $userId = $userModel->getInsertId();
        $token = $this->generateJwt($userId, $user['email']);
        var_dump($token);

        // Determina a expiração do cookie
        $expireTime = $rememberMe ? time() + (365 * 86400) : time() + 86400; // 1 ano ou 24 horas

        // O cookie token JWT
        $cookieOptions = [
            'name' => 'auth_token',
            'value' => $token,
            'expire' => $expireTime,
            'httponly' => true,
            'secure' => true, // em produção ?
        ];
        setcookie($cookieOptions['name'], $cookieOptions['value'], $cookieOptions['expire'], "/", "", $cookieOptions['secure'], $cookieOptions['httponly']);


        //Salvando da sessão     
        $this->session->set('loggedUser', $user);

        return redirect()->to('/site');
    }


    public function logout()
    {
        // REMOVENDO OS DADOS DO USUARIO DA SESSION 
        $this->session->remove('loggedUser');
        // Remove o cookie do token JWT
        $cookie = new Cookie('auth_token', '', [
            'expires'  => time() - 3600, //  passado
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
        ]);

        // Removendo cookie
        setcookie($cookie->getName(), '', $cookie->getExpiresTimestamp(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHTTPOnly());


        return  redirect()->to('/auth/login');
    }
}
