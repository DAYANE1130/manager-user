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
            return view('formForgotPassword');
        } else {
            return view('auth/login');
        }
    }


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

        // Cria link para rota que exibe o formulário
        $resetLink = base_url('password/ResetPasswordForm');

        // GUARDA O TOKEN NO BANCO

        $userId = (int)$user['id'];
        $userData = new \stdClass();
        $userData->password = $user['password'];

        $this->userModel->update($userId, $userData);

        // ENVIA EMAIL COM LINK DE REDEFINIÇÃO

        $this->sendResetEmail($email, $resetLink);

        // Armazena a mensagem na sessão
        $this->session->setFlashdata('sucess', 'Um e-mail foi enviado para sua caixa de entrada com instruções para redefinir sua senha.');


        return redirect()->to('auth/login')->with('success', 'Um link de redefinição de senha foi enviado para seu e-mail');
    }




    // MOSTRA FORMULARIO ao usuário PARA CRIAR NOVA SENHA
    public function resetPasswordForm()
    {
        //$token = $this->request->getGet();

        return view('formResetPassword');
    }

    // PERMITE O USUARIO MUDAR A SENHA 
    public function resetPassword()
    {
        $userModel = new UserModel();
        // $token = $this->request->getPost('token');

        // PEGA DADOS DA REQUISIÇÃO
        $userData = [
            'email' => $this->request->getPost('email'),
            'new_password' => $this->request->getPost('new_password'),
            'confirm_password' => $this->request->getPost('confirm_password')
        ];
        $userModel->setValidationContext('resetPassword');

        // VALIDA SENHA E email
        $userModel->setValidationContext('resetPassword');



        if (!$userModel->validate($userData)) {

            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }

        // BUSCA USUARIO
        $user = $this->userModel->where('email', $userData['email'])->first();

        if (!$user) {
            $errors = ['error' => 'Não existe um usuário com o e-mai informado'];
            return redirect()->back()->with('errors', $errors);
        }
        // ATUALIZA A SENHA E LIMPA TOKEN
        $userModel->where('email', $userData['email'])
            ->set(['password' => $userData['new_password']])
            ->update();

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
