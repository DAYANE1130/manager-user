<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

use Firebase\JWT\Key;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Authentication\JWTManager;
use CodeIgniter\Shield\Validation\ValidationRules;
use Exception;


class Password extends BaseController
{

    protected $userModel;

    public function __construct()
    {
        // Inicializando a propriedade com uma instância do UserModel
        $this->userModel = new UserModel();
    }


    //MOSTRA AO USER O FORM PARA REDEFINIR SENHA
    // public function forgotPassword()
    // {

    //     return view('forgotPasswordForm');
    // }


    public function forgotPassword()
    {
        // Verifica se o usuário está logado
        if (!$this->session->has('loggedUser')) {
            return view('forgotPasswordForm');
        }
    }


    // public function sendResetLink()
    // {

    //     //PEGa O EMAIL ENVIADO 
    //     $email = $this->request->getPost('email');
    //     $user = $this->userModel->where('email', $email)->first();

    //     if (!$user) {
    //         return redirect()->back()->with('error', 'O email não foi encontrado');
    //     }
    //     // CONFIRMANDO QUE USUARIO  NÃO ESTÁ DESLOGADO
    //     $loggedUser = session()->get('loggedUser');
    //     if (!$loggedUser) {

    //         // CRIA TOKEN PARA REDEFINIÇÃO DE SENHA
    //         $token = bin2hex(random_bytes(16));
    //         $resetLink = base_url('password/resetPasswordForm?token=' . $token);

    //         // GUARDA O TOKEN NO BANCO

    //         $userId = (int)$user['id'];
    //         $userData = new \stdClass();
    //         $userData->reset_token = $token;

    //         $this->userModel->update($userId, $userData);

    //         // ENVIA EMAIL COM LINK DE REDEFINIÇÃO

    //         $this->sendResetEmail($email, $resetLink, $token);

    //         return redirect()->to('password/forgotPasswordForm')->with('success', 'Um link de redefinição de senha foi enviado para seu e-mail');
    //     }
    // }

    public function sendResetLink()
    {

        // Pega o email enviado
        $email = $this->request->getPost('email');
        log_message('info', 'Email recebido: ' . $email); // Log para verificar o email recebido

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            log_message('error', 'Email não encontrado: ' . $email); // Log para email não encontrado
            return redirect()->back()->with('error', 'O email não foi encontrado');
        }

        // CRIA TOKEN PARA REDEFINIÇÃO DE SENHA
        $token = bin2hex(random_bytes(16));
        $resetLink = base_url('password/resetPasswordForm?token=' . $token);

        // GUARDA O TOKEN NO BANCO

        $userId = (int)$user['id'];
        $userData = new \stdClass();
        $userData->reset_token = $token;

        $this->userModel->update($userId, $userData);

        // ENVIA EMAIL COM LINK DE REDEFINIÇÃO

        $this->sendResetEmail($email, $resetLink, $token);

        // Armazena a mensagem na sessão
        $this->session->setFlashdata('sucess', 'Um e-mail foi enviado para sua caixa de entrada com instruções para redefinir sua senha.');


        return redirect()->to('auth/login')->with('success', 'Um link de redefinição de senha foi enviado para seu e-mail');
    }




    // MOSTRA FORMULARIO PARA CRIAR NOVA SENHA
    public function resetPasswordForm()
    {
        $token = $this->request->getGet('token');

        return view('resetPassword', ['token' => $token]);
    }

    // PERMITE O USUARIO MUDAR A SENHA 
    public function resetPassword()
    {
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('password');

        // BUSCA USUARIO
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token inválido ou expirado.');
        }
        // CCONFIRMANDO QUE USUÁRIO NÃO ESTÁ LOGADO
        $loggedUser = session()->get('loggedUser'); // Aqui, assumo que o e-mail do usuário está salvo na sessão
        if (!$loggedUser) {
            return redirect()->to('/auth/login')->with('error', 'Você precisa estar logado para alterar a senha.');
        }


        // VALIDA SENHA
        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'A senha deve ter pelo menos 6 caracteres.');
        }
        // OBS: FALTA AUTENTICAR COM O JWT   
        // ATUALIZA  A SENHA E LIMPA TOKEN
        // CRIAR UMA CAMADA DE SERVIÇO PARA REGRAS DE NEGOCIO? CONTROLLER MAIS LIMPO

        // $this->userModel->update($user['id'], [
        //     'password' => $newPassword,
        //     'reset_token' => null
        // ]);

        // Redireciona com mensagem de sucesso
        return redirect()->to('auth/login')->with('success', 'Senha redefinida com sucesso. Faça login.');
    }

    // MOVER PARA O ARQUIVO CORRETO DE CONFIGURAÇÕES
    // private function sendResetEmail($email, $resetLink, $token)
    // {
    //     $emailService = \Config\Services::email();

    //     $config['protocol'] = 'smtp';
    //     $config['SMTPHost'] = 'sandbox.smtp.mailtrap.io';
    //     $config['SMTPUser'] = '7a66dde1fb41a6';
    //     $config['SMTPPass'] = 'fb952627185cde';
    //     $config['SMTPPort'] = 2525;
    //     $config['mailType'] = 'html';
    //     $config['charset'] = 'utf-8';
    //     $config['wordWrap'] = true;

    //     $emailService->initialize($config);
    //     $emailService->setFrom('no-reply@seusite.com', 'Seu Site');
    //     $emailService->setTo($email);
    //     $emailService->setSubject('Redefinição de Senha');
    //     $emailService->setMessage("Clique no link a seguir para redefinir sua senha: <a href='{$resetLink}'>Redefinir Senha</a>, e seu token:" . $token);

    //     $emailService->send();
    // }

    public function clearSession()
    {
        $this->session->destroy();
        return redirect()->to('/'); // Redirecione para a página inicial ou outra página apropriada
    }

    private function sendResetEmail($email, $resetLink)
    {
        $emailService = \Config\Services::email();

        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = 'sandbox.smtp.mailtrap.io';
        $config['SMTPUser'] = '7a66dde1fb41a6';
        $config['SMTPPass'] = 'fb952627185cde';
        $config['SMTPPort'] = 2525;
        $config['mailType'] = 'html';
        $config['charset'] = 'utf-8';
        $config['wordWrap'] = true;

        $emailService->initialize($config);
        $emailService->setFrom('no-reply@seusite.com', 'Seu Site');
        $emailService->setTo($email);
        $emailService->setSubject('Redefinição de Senha');

        // Mensagem do e-mail
        $message = "
        <html>
        <head>
            <title>Redefinição de Senha</title>
        </head>
        <body>
            <p>Olá,</p>
            <p>Uma requisição para redefinir sua senha  foi efetuada. Para redefinir sua senha, clique no botão abaixo:</p>
            <p><a href='{$resetLink}' style='display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;'>Redefinir Senha</a></p>
            <p>Caso você não tenha realizado essa requisição, entre em contato conosco imediatamente em: <a href='mailto:contato@contato.com'>contato@contato.com</a></p>
            <p>Equipe Contato </p>
        </body>
        </html>
    ";

        $emailService->setMessage($message);

        // Envia o e-mail
        if (!$emailService->send()) {
            // Verifique por erros no envio do e-mail
            log_message('error', 'Erro ao enviar e-mail: ' . $emailService->printDebugger(['headers']));
        }
    }
}
