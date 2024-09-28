<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro</title>
</head>

<body>
  <h1>Cadastro de Usuário</h1>


  <?php
  if (session()->get('errors')) : ?>
    <div style="color: red">
      <ul>
        <?php foreach (session()->get('errors') as $error) : ?>
          <li> <?php echo esc($error) ?> </li>
        <?php endforeach ?>
      </ul>

    </div>
  <?php endif ?>

  <p>
    <a href="<?php echo base_url('auth/register'); ?>"></a>
    <?php if (isset($sucess)) { ?>
  <div>
    <?php echo esc($sucess); ?>
  </div>
<?php } ?>
</p>

<form action="<?php echo base_url('auth/register'); ?>" method="post" autocomplete="off">
  <label for="username">Nome de Usuário:</label>
  <input type="text" id="username" name="username" required>

  <br>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>

  <br>

  <label for="password">Senha:</label>
  <input type="password" id="password" name="password" required>

  <br>
  <label for="profile">Perfil:</label>
  <input type="text" id="profile" name="profile" required>
  <br>
  <button type="submit">Cadastrar</button>
</form>


</body>

</html>