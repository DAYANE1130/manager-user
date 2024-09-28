<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci minha senha</title>
</head>

<body>

  <h1>Redefinir Senha</h1>

  <!-- Verifica por mensagens de erro -->
  <?php if (session()->has('error')): ?>
    <div style="color: red;">
      <?= session('error') ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('password/sendResetLink') ?>" method="post">
    <label for="email">Digite seu e-mail:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Enviar link de redefinição</button>
  </form>

</body>

</html>