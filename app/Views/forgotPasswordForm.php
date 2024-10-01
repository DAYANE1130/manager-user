<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci minha senha</title>
</head>

<body>

  <h1>Esqueci minha senha</h1>
  <p> Insira seu e-mail para receber instruções sobre como redefinir sua senha.</p>

  <!-- Verifica por mensagens de erro -->
  <?php if (session()->has('error')): ?>
    <div style="color: red;">
      <?= session('error') ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('password/sendResetLink') ?>" method="post">
    <label for="email">Digite seu e-mail:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Enviar</button>
    <a href="<?= base_url('auth/login') ?>" style="display: inline-block; padding: 6px 12px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">Voltar</a>
  </form>

</body>

</html>