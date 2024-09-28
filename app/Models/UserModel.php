<?php

namespace App\Models;

use CodeIgniter\Model;

use function PHPUnit\Framework\isNull;

class UserModel extends Model
{
  protected $table = 'users';  // Nome da tabela no banco de dados
  protected $allowedFields = [
    'username',
    'email',
    'password',
    'profile',
    'reset_token'
    // 'email_verified_at',
    // 'reset_token',
    // 'reset_token_expires_at'
  ]; // Campos permitidos

  // Dates   
  protected $useTimestamps = true; // Alterado para true para usar created_at e updated_at
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';

  protected $beforeInsert = ['hashPassword']; // Garantindo que salve  a senha criptografa



  // Validation
  public $validationRules = [
    'username' => 'required|min_length[3]|max_length[255]|alpha_space',  // Adiciona a regra para aceitar apenas strings
    'email'    => 'required|valid_email|is_unique[users.email]',
    'password' => 'required|min_length[6]',
    'profile'  => 'required|in_list[user]',  // Aceita apenas 'user' como valor válido
  ];

  public $validationMessages = [
    'username' => [
      'required'    => 'O nome de usuário é obrigatório.',
      'min_length'  => 'O nome de usuário deve ter pelo menos 3 caracteres.',
      'max_length'  => 'O nome de usuário deve ter no máximo 255 caracteres.',
      'alpha_space' => 'O nome de usuário deve conter apenas letras e espaços.',  // Mensagem de erro para strings inválidas
    ],
    'email' => [
      'required'    => 'O e-mail é obrigatório.',
      'valid_email' => 'O e-mail fornecido não é válido.',
      'is_unique'   => 'Esse e-mail já está cadastrado.'
    ],
    'password' => [
      'required'   => 'A senha é obrigatória.',
      'min_length' => 'A senha deve ter pelo menos 6 caracteres.'
    ],
    'profile' => [
      'required'   => 'O tipo de perfil é obrigatório.',
      'in_list'    => 'O perfil deve ser "user".',  // Mensagem de erro caso o valor de 'profile' seja diferente de 'user'
    ]
  ];

  protected $skipValidation       = false;

  protected function hashPassword(array $data) // fazendo hash  de senha
  {
    if (isset($data['data']['password'])) {
      $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    }
    return $data;
  }
}
