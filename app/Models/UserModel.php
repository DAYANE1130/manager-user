<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{

  protected $table      = 'users';
  protected $primaryKey = 'id';

  protected $useAutoIncrement = true;

  protected $returnType     = 'array';
  protected $useSoftDeletes = false; // Ajustado, pois você não tem um campo 'deleted_at' na tabela

  protected $allowedFields = [
    'name',
    'email',
    'password',
    // 'email_verified_at',
    // 'reset_token',
    // 'reset_token_expires_at'
  ];

  protected bool $allowEmptyInserts = false;
  protected bool $updateOnlyChanged = true;

  // Dates
  protected $useTimestamps = true; // Alterado para true para usar created_at e updated_at
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';

  // Validation
  protected $validationRules      = [
    'name'     => 'required|min_length[3]|max_length[255]',
    'email'    => 'required|valid_email|is_unique[users.email]',
    'password' => 'required|min_length[8]'
  ];
  protected $validationMessages   = [
    'name' => [
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
    ]
  ];
  protected $skipValidation       = false;

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert   = ['hashPassword']; // Chama a função hashPassword antes de inserir
  protected $beforeUpdate   = ['hashPassword']; // Chama a função hashPassword antes de atualizar

  // Método para hash da senha
  protected function hashPassword(array $data)
  {
    if (isset($data['data']['password'])) {
      $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
    }
    return $data;
  }
}
