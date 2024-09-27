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


    // Cadastro de usuário
    public function register()
    {
        $authService = service('auth');
        // Verifica se a requisição é GET para renderizar a view
        if ($this->request->getMethod() === 'GET') {
            return view('register');  // Renderiza a view register.php
        }

        // Caso contrário, trata como POST (processa o cadastro)
        $userModel = model('App\Models\UserModel');

        // Dados do usuário a serem salvos
        $userData = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'profile'  => $this->request->getPost('profile')
        ];

        //E-mail é unico no banco
        // $findUserByEmail = $userModel->where('email', $userData['email'])->first();

        // CRIANDO USER NO BANCO -- Verificar validações da model, não está validando 
        if (!$userModel->save($userData)) {
            // Salva o novo usuário
            return view('register', [
                'validation' => $userModel->errors()
            ]);
            // return $this->response->setStatusCode(400)->setJSON([
            //     'status' => 'error',
            //     'errors' => $userModel->errors()
            // ]);
        }


        // Gera o token JWT , COLOCAR EM UM HELPER/ UTILS
        $key = getenv('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600 * 24;  // Token válido por 24 horas
        $payload = [
            'iss' => 'theissuer',
            'aud' => 'theaudience',
            'iat' => $iat,
            'exp' => $exp,
            'email' => $userData['email'],
            'username' => $userData['username'],
            'profile' => $userData['profile']
        ];

        // Codifica o token JWT
        $token = JWT::encode($payload, $key, 'HS256');
        var_dump($token);

        // Salvando user na session
        $this->session->set('loggedUser', $userData);

        return redirect()->to('/site')->with('sucess', 'Usuário criado com sucesso');
    }

    // Login do usuário
    public function login()
    {
        // Verifica se a requisição é GET para renderizar a view
        if ($this->request->getMethod() === 'get') {
            return view('login');  // Renderiza a view register.php
        }

        $userModel = model('App\Models\UserModel');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        var_dump($password, $email);

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return view('login', [
                'error' => 'E-mail ou senha inválidos'
            ]);
        }
        if ($user) {
            // Gerar token JWT
            $key = getenv('JWT_SECRET');
            $iat = time();
            $exp = $iat + 3600 * 24; // Token válido por 24 horas
            $payload = [
                'iss' => 'theissuer',
                'aud' => 'theaudience',
                'iat' => $iat,
                'exp' => $exp,
                'email' => $user['email'],
                'username' => $user['username'],
                'profile' => $user['profile']
            ];
            //FAZER DEPOIS A FUNÇÃO DE VALIDAR O TOKEN  (isso dará autorização a rotas necessárias)

            $token = JWT::encode($payload, $key, 'HS256');

            var_dump($token);

            // o cookie token JWT // criar função para reutilizar
            $cookieOptions = [
                'name' => 'auth_token',
                'value' => $token,
                'expire' => time() + 86400, // 24 horas
                'httponly' => true,
                'secure' => true, // em produção ?
            ];
            setcookie($cookieOptions['name'], $cookieOptions['value'], $cookieOptions['expire'], "/", "", $cookieOptions['secure'], $cookieOptions['httponly']);

            //Salvando da sessão     
            $this->session->set('loggedUser', $user);
            print_r($_SESSION);

            return redirect()->to('site');
        } else {
            return redirect()->to('login');
        }
    }


    public function logout()
    {

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


        redirect('login');
    }
}
