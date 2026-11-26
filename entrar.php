<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $consulta = $conexao->prepare(
        "SELECT * FROM usuarios WHERE email = ?"
    );

    $consulta->execute([$email]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        if ($usuario['tipo'] === 'admin') {

            header("Location: painel/index.php");

       } else {

    if (($_GET['checkout'] ?? '') === '1') {

        header("Location: checkout.php");

    } else {

        header("Location: index.php");

    }

}

        exit;

    } else {

        $mensagem = "E-mail ou senha incorretos.";

    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Entrar | Next Level</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="estilo.css">

</head>

<body>

<header class="topo text-center">

    <a href="index.php" class="marca">

        NEXT <span>LEVEL</span>

    </a>

</header>

<main class="area-conta">

    <div class="card-conta">

        <span class="categoria-produto">
            MINHA CONTA
        </span>

        <h1>Entrar</h1>

        <p class="texto-conta">

            Acesse sua conta Next Level.

        </p>

        <?php if (($_GET['cadastro'] ?? '') === 'sucesso'): ?>

            <div class="alert alert-success">

                Cadastro realizado com sucesso!

            </div>

        <?php endif; ?>

        <?php if ($mensagem !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    E-mail
                </label>

                <input type="email"
                       name="email"
                       class="campo-conta"
                       required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Senha
                </label>

                <input type="password"
                       name="senha"
                       class="campo-conta"
                       required>

            </div>

            <button type="submit"
                    class="btn-next w-100">

                Entrar na minha conta

            </button>

        </form>

        <p class="link-conta">

            Ainda não possui uma conta?

            <a href="registrar.php">Cadastre-se</a>

        </p>

        <a href="index.php" class="voltar-loja">

            ← Voltar para a loja

        </a>

    </div>

</main>

<footer class="rodape">

    <strong>NEXT LEVEL</strong>

    <p class="mt-2 mb-0">

        Loja de acessórios gamers | Projeto acadêmico - 2026

    </p>

</footer>

<script src="script.js"></script>

</body>
</html>