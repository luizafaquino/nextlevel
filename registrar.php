<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];

    if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Preencha os dados corretamente.";

    } elseif (strlen($senha) < 6) {

        $mensagem = "A senha deve ter pelo menos 6 caracteres.";

    } elseif ($senha !== $confirmar) {

        $mensagem = "As senhas não coincidem.";

    } else {

        $consulta = $conexao->prepare(
            "SELECT id FROM usuarios WHERE email = ?"
        );

        $consulta->execute([$email]);

        if ($consulta->fetch()) {

            $mensagem = "Este e-mail já está cadastrado.";

        } else {

            $senhaProtegida = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO usuarios
                    (nome, email, senha, tipo)
                    VALUES (?, ?, ?, 'cliente')";

            $consulta = $conexao->prepare($sql);

            $consulta->execute([
                $nome,
                $email,
                $senhaProtegida
            ]);

            header("Location: entrar.php?cadastro=sucesso");
            exit;

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Criar conta | Next Level</title>

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

        <h1>Criar conta</h1>

        <p class="texto-conta">
            Cadastre-se para realizar suas compras.
        </p>

        <?php if ($mensagem !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Nome completo
                </label>

                <input type="text"
                       name="nome"
                       class="campo-conta"
                       maxlength="100"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    E-mail
                </label>

                <input type="email"
                       name="email"
                       class="campo-conta"
                       maxlength="150"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Senha
                </label>

                <input type="password"
                       name="senha"
                       class="campo-conta"
                       minlength="6"
                       required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Confirmar senha
                </label>

                <input type="password"
                       name="confirmar"
                       class="campo-conta"
                       minlength="6"
                       required>

            </div>

            <button type="submit"
                    class="btn-next w-100">

                Criar minha conta

            </button>

        </form>

        <p class="link-conta">

            Já possui uma conta?

            <a href="entrar.php">Entrar</a>

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