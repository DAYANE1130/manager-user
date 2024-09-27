<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página Inicial</title>
  <style>
  </style>
</head>

<body>
  <div class="container">
    <?php if (isset($loggedUser)): ?>
      <h1>Bem-vindo!. <?php esc($loggedUser['username']) ?> </h1>
      <p>Você já está logado. Por favor, escolha uma opção:</p>
    <?php endif; ?>

    <button><a href="/cadastrar-produto">Cadastrar Produto</a></button>
    <button><a href="/ver-produtos">Ver Produtos</a></button>
    <button><a href="/gerenciar-pedidos">Gerenciar Pedidos</a></button>
    <button>Sair</></button>
  </div>
</body>

</html>