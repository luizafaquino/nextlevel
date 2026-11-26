<?php

session_name("nextlevel_session");
session_start();

require_once "../configuracao/banco.php";

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {

    header("Location: ../entrar.php");
    exit;

}

$sql = "SELECT pedidos.*, usuarios.nome AS cliente,
        usuarios.email
        FROM pedidos
        INNER JOIN usuarios
        ON pedidos.usuario_id = usuarios.id
        ORDER BY pedidos.id DESC";

$pedidos = $conexao->query($sql)
    ->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pedidos | Next Level</title>

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

            <a href="index.php">Painel</a>

            <a href="gerenciar.php">Produtos</a>

            <a href="../index.php">Ver loja</a>

            <a href="../sair.php">Sair</a>

        </nav>

    </div>

</header>

<main class="container py-5">

    <a href="index.php" class="voltar-loja">

        ← Voltar ao painel

    </a>

    <h1 class="titulo-catalogo mt-4">

        Pedidos realizados

    </h1>

    <p class="contagem mb-4">

        <?= count($pedidos) ?> pedidos registrados

    </p>

    <?php if (empty($pedidos)): ?>

        <div class="bloco-admin">

            <p class="mb-0">

                Nenhum pedido foi realizado até o momento.

            </p>

        </div>

    <?php endif; ?>

    <?php foreach ($pedidos as $pedido): ?>

        <?php

        $sql = "SELECT itens_pedido.*,
                produtos.nome AS produto
                FROM itens_pedido
                INNER JOIN produtos
                ON itens_pedido.produto_id = produtos.id
                WHERE itens_pedido.pedido_id = ?";

        $consulta = $conexao->prepare($sql);

        $consulta->execute([$pedido['id']]);

        $itens = $consulta->fetchAll(PDO::FETCH_ASSOC);

        ?>

        <div class="bloco-admin mb-4">

            <div class="d-flex flex-wrap
                        justify-content-between
                        align-items-center gap-3 mb-3">

                <h2 class="mb-0">

                    Pedido #<?= $pedido['id'] ?>

                </h2>

                <span class="valor-admin">

                    R$ <?= number_format($pedido['total'], 2, ',', '.') ?>

                </span>

            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-4">

                    <p class="contagem mb-1">Cliente</p>

                    <strong>

                        <?= htmlspecialchars($pedido['cliente']) ?>

                    </strong>

                    <p class="contagem">

                        <?= htmlspecialchars($pedido['email']) ?>

                    </p>

                </div>

                <div class="col-md-4">

                    <p class="contagem mb-1">Data do pedido</p>

                    <strong>

                        <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?>

                    </strong>

                </div>

                <div class="col-md-4">

                    <p class="contagem mb-1">Pagamento</p>

                    <strong>

                        <?= htmlspecialchars($pedido['forma_pagamento']) ?>

                    </strong>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-dark tabela-admin mb-0">

                    <thead>

                        <tr>

                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Valor unitário</th>
                            <th>Subtotal</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($itens as $item): ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($item['produto']) ?>

                                </td>

                                <td>

                                    <?= $item['quantidade'] ?>

                                </td>

                                <td>

                                    R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?>

                                </td>

                                <td class="valor-admin">

                                    R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php endforeach; ?>

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