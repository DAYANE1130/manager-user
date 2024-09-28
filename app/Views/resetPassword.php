<!DOCTYPE html>
<html>

<head>
  <title>Redefinir Senha</title>
</head>

<body>
  <h2>Redefinir Senha</h2>
  <form action="<?= base_url('password/resetPassword') ?>" method="post">
    <input type="hidden" name="token" value="<?= esc($token) ?>" />
    <label for="password">Nova Senha:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Redefinir Senha</button>
  </form>
</body>

</html>