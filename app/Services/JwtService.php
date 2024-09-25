<?php
// app/Services/JwtService.php
namespace App\Services;

use Firebase\JWT\JWT;

class JwtService
{
  private $key;

  public function __construct()
  {
    $this->key = getenv('JWT_SECRET'); // Chave secreta (deve estar em .env)
  }

  public function generateToken($userData)
  {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token expira em 1 hora
    $payload = [
      'iat' => $issuedAt,
      'exp' => $expirationTime,
      'data' => $userData
    ];

    return JWT::encode($payload, $this->key, 'HS256');
  }
}
