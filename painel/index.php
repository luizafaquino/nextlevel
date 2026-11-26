<?php

session_name("nextlevel_session");
session_start();

require_once "../configuracao/banco.php";

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {

    header("Location: ../entrar.php");
    exit;

}

$totalProdutos = $conexao->query(
    "SELECT COUNT(*) FROM produtos"
)->fetchColumn();

$totalPedidos = $conexao->query(
    "SELECT COUNT(*) FROM pedidos"
)->fetchColumn();

$totalClientes = $conexao->query(
    "SELECT COUNT(*) FROM usuarios WHERE tipo = 'cliente'"
)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Painel administrativo | Next Level</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="../estilo.css">

</head>

<body>

<header class="topo">

    <div class="container d-flex flex-wrap
                justify-content-between align-items-center gap-3">

        <a href="index.php" class="marca">

            NEXT <span>LEVEL</span>

        </a>

        <nav class="links-topo">

            <a href="../index.php">Ver loja</a>

            <a href="../sair.php">Sair</a>

        </nav>

    </div>

</header>

<main class="container py-5">

    <span class="categoria-produto">

        ÁREA ADMINISTRATIVA

    </span>

    <h1 class="titulo-catalogo mt-3">

        Painel de controle

    </h1>

    <p class="contagem mb-4">

        Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!

        Gerencie sua loja Next Level.

    </p>

    <div class="row g-3 mb-5">

        <div class="col-md-4">

            <div class="indicador-admin">

                <i class="bi bi-box-seam"></i>

                <p>Produtos cadastrados</p>

                <strong><?= $totalProdutos ?></strong>

            </div>

        </div>

        <div class="col-md-4">

            <div class="indicador-admin">

                <i class="bi bi-bag-check"></i>

                <p>Pedidos realizados</p>

                <strong><?= $totalPedidos ?></strong>

            </div>

        </div>

        <div class="col-md-4">

            <div class="indicador-admin">

                <i class="bi bi-people"></i>

                <p>Clientes cadastrados</p>

                <strong><?= $totalClientes ?></strong>

            </div>

        </div>

    </div>

    <h2 class="mb-4">Gerenciamento</h2>

    <div class="row g-4">

        <div class="col-md-6">

            <div class="bloco-admin">

                <i class="bi bi-box-seam"></i>

                <h2>Produtos</h2>

                <p>

                    Cadastre, edite e exclua produtos.
                    Gerencie preços e estoques.

                </p>

                <a href="gerenciar.php"
                   class="btn-next d-block text-center">

                    Gerenciar produtos

                </a>

            </div>

        </div>

        <div class="col-md-6">

            <div class="bloco-admin">

                <i class="bi bi-receipt"></i>

                <h2>Pedidos</h2>

                <p>

                    Consulte os pedidos, clientes,
                    valores e formas de pagamento.

                </p>

                <a href="vendas.php"
                   class="btn-next d-block text-center">

                    Visualizar pedidos

                </a>

            </div>

        </div>

    </div>

</main>

<footer class="rodape">

    <strong>NEXT LEVEL</strong>

    <p class="mt-2 mb-0">

        Painel administrativo | Projeto acadêmico - 2026

    </p>

</footer>

<script src="../script.js"></script>

</body>
</html>