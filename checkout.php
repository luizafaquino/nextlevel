<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

// Verificar se o cliente está logado

if (!isset($_SESSION['usuario_id'])) {

    header("Location: entrar.php?checkout=1");

}

$carrinho = $_SESSION['carrinho'] ?? [];

$mensagem = "";
$sucesso = false;
$pedidoId = 0;
$total = 0;
$itens = [];
$estoqueValido = true;
$pagamento = "";

// Verificar se o carrinho está vazio

if (empty($carrinho)) {

    header("Location: carrinho.php");
    exit;

}

// Buscar os produtos

foreach ($carrinho as $id => $quantidade) {

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$produto || $quantidade < 1 ||
        $quantidade > $produto['estoque']) {

        $estoqueValido = false;
        continue;

    }

    $produto['quantidade'] = $quantidade;

    $produto['subtotal'] =
        $produto['preco'] * $quantidade;

    $total += $produto['subtotal'];

    $itens[] = $produto;

}

// Finalizar pedido

if (isset($_POST['finalizar'])) {

    $pagamento = $_POST['pagamento'] ?? '';

    $formasPermitidas = [
        'PIX',
        'Boleto',
        'Cartão'
    ];

    if (!in_array($pagamento, $formasPermitidas, true)) {

        $mensagem = "Selecione uma forma de pagamento.";

    } elseif (!$estoqueValido) {

        $mensagem = "Existem produtos com estoque insuficiente.";

    } else {

        try {

            $conexao->beginTransaction();

            $produtosPedido = [];
            $total = 0;

            // Conferir novamente o estoque

            ksort($carrinho);

            foreach ($carrinho as $id => $quantidade) {

                $consulta = $conexao->prepare(
                    "SELECT * FROM produtos
                     WHERE id = ? FOR UPDATE"
                );

                $consulta->execute([$id]);

                $produto = $consulta->fetch(PDO::FETCH_ASSOC);

                if (!$produto ||
                    $quantidade < 1 ||
                    $quantidade > $produto['estoque']) {

                    throw new Exception(
                        "Estoque insuficiente."
                    );

                }

                $produto['quantidade'] = $quantidade;

                $total += $produto['preco'] * $quantidade;

                $produtosPedido[] = $produto;

            }

            // Registrar pedido

            $sql = "INSERT INTO pedidos
                    (usuario_id, forma_pagamento, total)
                    VALUES (?, ?, ?)";

            $consulta = $conexao->prepare($sql);

            $consulta->execute([
                $_SESSION['usuario_id'],
                $pagamento,
                $total
            ]);

            $pedidoId = $conexao->lastInsertId();

            // Registrar itens e atualizar estoque

            foreach ($produtosPedido as $produto) {

                $sql = "INSERT INTO itens_pedido
                        (pedido_id, produto_id,
                         quantidade, preco_unitario)
                        VALUES (?, ?, ?, ?)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    $pedidoId,
                    $produto['id'],
                    $produto['quantidade'],
                    $produto['preco']
                ]);

                $sql = "UPDATE produtos
                        SET estoque = estoque - ?
                        WHERE id = ?";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    $produto['quantidade'],
                    $produto['id']
                ]);

            }

            $conexao->commit();

            // Limpar carrinho

            $_SESSION['carrinho'] = [];

            $sucesso = true;

        } catch (Exception $e) {

            if ($conexao->inTransaction()) {

                $conexao->rollBack();

            }

            $mensagem = "Não foi possível concluir a compra.
                         Confira o estoque e tente novamente.";

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

    <title>Finalizar compra | Next Level</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="estilo.css">

</head>

<body>

<header class="topo">

    <div class="container d-flex flex-wrap
                justify-content-between align-items-center gap-3">

        <a href="index.php" class="marca">

            NEXT <span>LEVEL</span>

        </a>

        <nav class="links-topo">

            <a href="index.php">Produtos</a>

            <a href="carrinho.php">

                <i class="bi bi-cart3"></i>

                Carrinho

            </a>

            <a href="sair.php">Sair</a>

        </nav>

    </div>

</header>

<main class="container py-5">

    <?php if ($sucesso): ?>

        <!-- Compra concluída -->

        <div class="pedido-sucesso">

            <i class="bi bi-check-circle-fill"></i>

            <h1>Compra realizada com sucesso!</h1>

            <p>

                Obrigado,
                <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!

            </p>

            <div class="dados-pedido">

                <p>

                    Número do pedido:

                    <strong>#<?= $pedidoId ?></strong>

                </p>

                <p>

                    Forma de pagamento:

                    <strong>

                        <?= htmlspecialchars($pagamento) ?>

                    </strong>

                </p>

                <p class="preco-detalhes">

                    R$ <?= number_format($total, 2, ',', '.') ?>

                </p>

            </div>

            <p class="contagem">

                Pagamento simulado para fins acadêmicos.

            </p>

            <a href="index.php" class="btn-next">

                Voltar para a loja

            </a>

        </div>

    <?php else: ?>

        <a href="carrinho.php" class="voltar-loja">

            <i class="bi bi-arrow-left"></i>

            Voltar ao carrinho

        </a>

        <h1 class="titulo-catalogo mt-4">

            Finalizar compra

        </h1>

        <p class="contagem mb-4">

            Confira seu pedido e escolha a forma de pagamento.

        </p>

        <?php if ($mensagem !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>

        <?php if (!$estoqueValido): ?>

            <div class="alert alert-warning">

                Alguns produtos estão com estoque insuficiente.
                Volte ao carrinho para ajustar as quantidades.

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="row g-4">

                <div class="col-lg-7">

                    <!-- Dados do cliente -->

                    <div class="bloco-checkout">

                        <h2>

                            <i class="bi bi-person"></i>

                            Dados do cliente

                        </h2>

                        <p class="mb-0">

                            Olá,

                            <strong>

                                <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!

                            </strong>

                            Você está conectado à sua conta.

                        </p>

                    </div>

                    <!-- Pagamento -->

                    <div class="bloco-checkout">

                        <h2>

                            <i class="bi bi-credit-card"></i>

                            Forma de pagamento

                        </h2>

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="PIX"
                                   required>

                            <i class="bi bi-qr-code"></i>

                            <div>

                                <strong>PIX</strong>

                                <small>Pagamento instantâneo</small>

                            </div>

                        </label>

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="Boleto"
                                   required>

                            <i class="bi bi-upc-scan"></i>

                            <div>

                                <strong>Boleto bancário</strong>

                                <small>Pagamento por boleto</small>

                            </div>

                        </label>

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="Cartão"
                                   required>

                            <i class="bi bi-credit-card"></i>

                            <div>

                                <strong>Cartão de crédito</strong>

                                <small>Pagamento com cartão</small>

                            </div>

                        </label>

                        <p class="contagem mt-3 mb-0">

                            Todos os pagamentos são simulados.

                        </p>

                    </div>

                </div>

                <!-- Resumo do pedido -->

                <div class="col-lg-5">

                    <div class="resumo-compra">

                        <h2>Resumo do pedido</h2>

                        <?php foreach ($itens as $item): ?>

                            <div class="produto-checkout">

                                <div>

                                    <strong>

                                        <?= htmlspecialchars($item['nome']) ?>

                                    </strong>

                                    <small>

                                        Quantidade:
                                        <?= $item['quantidade'] ?>

                                    </small>

                                </div>

                                <span>

                                    R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                        <hr>

                        <div class="linha-resumo total-compra">

                            <span>Total</span>

                            <strong>

                                R$ <?= number_format($total, 2, ',', '.') ?>

                            </strong>

                        </div>

                        <button type="submit"
                                name="finalizar"
                                class="btn-next w-100 mt-3"
                                <?= !$estoqueValido ? 'disabled' : '' ?>>

                            <i class="bi bi-check-circle"></i>

                            Confirmar pedido

                        </button>

                        <a href="carrinho.php"
                           class="voltar-loja d-block text-center mt-3">

                            Voltar ao carrinho

                        </a>

                    </div>

                </div>

            </div>

        </form>

    <?php endif; ?>

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