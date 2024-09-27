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
  protected $validationRules = [
    'username'     => 'required|min_length[3]|max_length[255]',
    'email'    => 'required|valid_email|is_unique[users.email]',
    'password' => 'required|min_length[6]',
    'profile' => 'required',
  ];
  protected $validationMessages   = [
    'username' => [
      'required'    => 'O nome é obrigatório.',
      'min_length'  => 'O nome deve ter pelo menos 3 caracteres.',
      'max_length'  => 'O nome deve ter no máximo 255 caracteres.'
    ],
    'email' => [
      'required'    => 'O e-mail é obrigatório.',
      'valid_email' => 'O e-mail fornecido não é válido.',
      'is_unique'   => 'Esse e-mail já está cadastrado.'
    ],
    'password' => [
      'required'   => 'A senha é obrigatória.',
      'min_length' => 'A senha deve ter pelo menos 8 caracteres.'
    ],
    'profile' => [
      'required'   => 'O tipo de perfil é obrigatório.',
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
