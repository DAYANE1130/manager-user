<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>

<body>
    <h1>Faça seu login</h1>

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

    <form action="<?= base_url('auth/login') ?>" method="post">
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br>

        <input type="checkbox" id="remember_me" name="remember_me">
        <label for="remember_me">Manter conectado</label>
        <br>

        <button type="submit">Login</button>

        <p>
            <a href="<?= base_url('password/forgotPassword') ?>">Esqueci a Senha</a>
        </p>

    </form>
</body>

</html>