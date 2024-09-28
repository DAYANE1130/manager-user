<?php

namespace App\Controllers;

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\Controller;
use Exception;

class User extends BaseController
{
  public function changePassword()
  {
    $userModel = new UserModel();


    // Se a requisição for GET, retorno a view do formulário para alterar a senha
    if ($this->request->getMethod() === 'GET') {
      return view('changePassword');
    }

    // Primeiro, pego o token JWT do cookie
    $token = $_COOKIE['auth_token'] ?? null;

    // Verifico se a sessão do usuário está ativa
    $loggedUser = session()->get('loggedUser'); // Aqui, assumo que o e-mail do usuário está salvo na sessão
    if (!$loggedUser) {
      return redirect()->to('/auth/login')->with('error', 'Você precisa estar logado para alterar a senha.');
    }

    // Se for uma requisição POST, capturo os dados do formulário
    $newPassword = $this->request->getPost('new_password');
    $currentPassword = $this->request->getPost('current_password');

    // Valido se a nova senha tem pelo menos 6 caracteres
    if (strlen($newPassword) < 6) {
      return redirect()->back()->with('error', 'A nova senha deve ter pelo menos 6 caracteres.');
    }

    // Recupero os dados do usuário usando o token
    $userData = $this->validateJwt($token);

    if (!$userData) {
      return redirect()->to('/auth/login')->with('error', 'Token inválido ou expirado.');
    }

    $email = $loggedUser['email']; // Aqui, pego o e-mail do usuário que está na sessão

    // Busco o usuário no banco de dados pelo e-mail recuperado da sessão
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
      return redirect()->back()->with('error', 'Usuário não encontrado.');
    }

    // Verifico se a senha atual está correta
    if (!password_verify($currentPassword, $user['password'])) {
      return redirect()->back()->with('error', 'A senha atual está incorreta.');
    }

    // Cria um objeto e define a propriedade 'password'
    $passwordUpdate = new \stdClass();
    $passwordUpdate->password = $newPassword;

    // Atualizo a senha do usuário com o objeto
    $userModel->update($user['id'], $passwordUpdate);

    // Redireciono para a página de login com sucesso
    return redirect()->to('/auth/login')->with('success', 'Senha alterada com sucesso.');
  }

  // Método para validar o JWT
  private function validateJwt($token)
  {
    // Aqui, gero a chave secreta do ambiente
    $key = getenv('JWT_SECRET');

    try {
      // Decodifico o token JWT
      $decoded = JWT::decode($token, new Key($key, 'HS256'));

      // Verifico se o token já expirou
      if ($decoded->exp < time()) {
        return false; // Se expirou, retorno falso
      }

      // Se tudo estiver certo, retorno os dados do token
      return (array) $decoded; // Transformo em array para facilitar o uso
    } catch (Exception $e) {
      // Se ocorrer um erro, trato aqui
      return false; // Retorno falso se o token for inválido ou erro na decodificação
    }
  }
}
