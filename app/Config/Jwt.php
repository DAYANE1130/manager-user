<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jwt extends BaseConfig
{
  public $key = getenv('JWT_SECRET');
  public $algorithm = 'HS256'; // Algoritmo para assinar o token
  public $expiresIn = 30 * 24 * 60 * 60; // Expiração do token em segundos (30 dias)

}
