<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página Inicial</title>
  <style>
  </style>

  <script>
    function confirmLogout() {
      return confirm('Você realmente deseja sair?')
    }
  </script>
</head>

<body>
  <div class="container">
    <?php if (isset($loggedUser) && $loggedUser): ?>
      <h1>Bem-vindo(a), <?= esc($loggedUser['username']) ?>!</h1> <!-- Exibe o nome do usuário -->
      <p>Você já está logado. Por favor, escolha uma opção:</p>
    <?php else: ?>
      <h1>Bem-vindo, visitante!</h1>
      <p>Por favor, faça login para continuar.</p>
    <?php endif; ?>

    <button><a href="/cadastrar-produto">Cadastrar Produto</a></button>
    <button><a href="/ver-produtos">Ver Produtos</a></button>
    <button><a href="/gerenciar-pedidos">Gerenciar Pedidos</a></button>
    <button><a href=" <?= base_url('auth/logout') ?>" onclick="return confirmLogout();">Sair</a></button>

    <p>
      <a href="<?= base_url('user/changePassword') ?>">Alterar a Senha</a>
    </p>

  </div>
</body>

</html>