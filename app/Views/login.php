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
    //<form action="<?= base_url('authController/register/' . $plan->id) 
    ?>


    <form action="<?= base_url('auth/login') ?>" method="post">


        <br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <br>

        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>

        <br>

        <button type="submit">Login</button>

        <!-- Link para a página de "Esqueci a Senha" -->
        <p>
            <a href="<?= base_url('passwordController/forgotPassword') ?>">Esqueci a Senha</a>
            <?php if (isset($sucess)): ?>
        <div>
            <?= esc($sucess) ?>
        </div>
    <?php endif ?>
    </p>
    </form>


</body>

</html>