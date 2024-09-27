<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class PasswordController extends BaseController
{

    protected $userModel;

    public function __construct()
    {
        // Inicializando a propriedade com uma instância do UserModel
        $this->userModel = new UserModel();
    }


    //MOSTRA AO USER O FORM PARA REDEFINIR SENHA
    public function forgotPassword()
    {
        return view('forgotPassword');
    }
    public function sendResetLink()
    {

        //PEGa O EMAIL ENVIADO 
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'O email não foi encontrado');
        }

        // CRIA TOKEN PARA REDEFINIÇÃO DE SENHA
        $token = bin2hex(random_bytes(16));
        $resetLink = base_url('PasswordController/resetPasswordForm?token=' . $token);

        // GUARDA O TOKEN NO BANCO

        $userId = (int)$user['id'];
        $userData = new \stdClass();
        $userData->reset_token = $token;

        $this->userModel->update($userId, $userData);

        // ENVIA EMAIL COM LINK DE REDEFINIÇÃO

        $this->sendResetEmail($email, $resetLink, $token);

        return redirect()->back()->with('success', 'Um link de redefinição de senha foi enviado para seu e-mail');
    }





    // MOSTRA FORMULARIO PARA CRIAR NOVA SENHA
    public function resetPasswordForm()
    {
        $token = $this->request->getGet('token');

        return view('resetPassword', ['token' => $token]);
    }


    public function resetPassword()
    {
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('password');

        // BUSCA USUARIO
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token inválido ou expirado.');
        }

        // VALIDA SENHA
        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'A senha deve ter pelo menos 6 caracteres.');
        }

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
    private function sendResetEmail($email, $resetLink, $token)
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
        $emailService->setMessage("Clique no link a seguir para redefinir sua senha: <a href='{$resetLink}'>Redefinir Senha</a>, e seu token:" . $token);

        $emailService->send();
    }
}
