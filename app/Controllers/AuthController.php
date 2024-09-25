<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\JwtService;

use PhpParser\Node\Stmt\TryCatch;
use Exception;

class AuthController extends ResourceController
{

  protected $jwtService;
  public function __construct()
  {
    $this->jwtService = new JwtService(); // disponível para todo o controller
  }
  public function register()
  {


    $userModel = new UserModel();

    // Pega dados da requisição;
    $data = $this->request->getJson(true);

    var_dump($data);
    // Verifica se o e-mail já está cadastrado
    $existingUser = $userModel->where('email', $data['email'])->first();
    if ($existingUser) {
      return $this->fail('O e-mail já está cadastrado.', 400);
    }

    // Cadastra user no banco
    try {
      if (!$userModel->save($data)) {
        return $this->fail($userModel->errors(), 400);
      }

      $token = $this->jwtService->generateToken([
        'id' => $userModel->getInsertID(), // pegando o id do usuario criado 
        "email" => $data['email']
      ]);

      // Retorna o token JWT
      return $this->respond(['status' => 'success', 'token' => $token], 200);
    } catch (Exception $e) {
      return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $e->getMessage()]);
    }
  }
}
