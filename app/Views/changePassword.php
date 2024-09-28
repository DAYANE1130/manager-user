<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alterar Senha</title>
</head>

<body>
  <h1>Alterar Senha</h1>

  <?php if (session()->getFlashdata('error')): ?>
    <div style="color: red;"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('success')): ?>
    <div style="color: green;"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (isset($loggedUser) && $loggedUser): ?>
    <h1>Bem-vindo, <?= esc($loggedUser['username']) ?>!</h1> <!-- Exibe o nome do usuário -->
  <?php endif; ?>

  <form action="<?= base_url('user/changePassword') ?>" method="post">
    <label for="current_password">Senha Atual:</label>
    <input type="password" name="current_password" required>
    <br>

    <label for="new_password">Nova Senha:</label>
    <input type="password" name="new_password" required>
    <br>
    <button type="submit">Alterar a senha</button>
  </form>
</body>

</html>