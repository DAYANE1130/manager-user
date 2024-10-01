<!DOCTYPE html>
<html>

<head>
  <title>Redefinir Senha</title>
</head>

<body>
  <h2>Redefinir Senha</h2>
  <?php
  if (session()->get('errors')) : ?>
    <div style="color: red">
      <ul>
        <?php foreach (session()->get('errors') as $error) : ?>
          <li> <?php echo esc($error) ?> </li>
        <?php endforeach ?>
      </ul>

    </div>
  <?php endif; ?>
  <form action="<?= base_url('password/resetPassword') ?>" method="post">

    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <label for="password">Nova Senha:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <label for="new-password">Confirmar senha:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <button type="submit">Enviar</button>
  </form>
</body>

</html>