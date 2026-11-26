<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$mensagem = "";

// Adicionar produto

if (isset($_POST['adicionar'])) {

    $id = (int) $_POST['produto_id'];
    $quantidade = (int) $_POST['quantidade'];

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto && $quantidade > 0) {

        $quantidadeAtual = $_SESSION['carrinho'][$id] ?? 0;

        $novaQuantidade = $quantidadeAtual + $quantidade;

        if ($novaQuantidade <= $produto['estoque']) {

            $_SESSION['carrinho'][$id] = $novaQuantidade;

            header("Location: carrinho.php");
            exit;

        } else {

            $mensagem = "Quantidade superior ao estoque disponível.";

        }
    }
}

// Atualizar quantidade

if (isset($_POST['atualizar'])) {

    $id = (int) $_POST['produto_id'];
    $quantidade = (int) $_POST['quantidade'];

    $consulta = $conexao->prepare(
        "SELECT estoque FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto &&
        isset($_SESSION['carrinho'][$id]) &&
        $quantidade > 0 &&
        $quantidade <= $produto['estoque']) {

        $_SESSION['carrinho'][$id] = $quantidade;

        header("Location: carrinho.php");
        exit;

    } else {

        $mensagem = "Quantidade inválida ou superior ao estoque.";

    }
}

// Remover produto

if (isset($_POST['remover'])) {

    $id = (int) $_POST['produto_id'];

    unset($_SESSION['carrinho'][$id]);

    header("Location: carrinho.php");
    exit;

}

// Buscar produtos do carrinho

$itens = [];
$total = 0;
$estoqueValido = true;

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto) {

        $produto['quantidade'] = $quantidade;

        $produto['subtotal'] =
            $produto['preco'] * $quantidade;

        $total += $produto['subtotal'];

        if ($quantidade > $produto['estoque']) {
            $estoqueValido = false;
        }

        $itens[] = $produto;

    } else {

        unset($_SESSION['carrinho'][$id]);

    }
}

$quantidadeCarrinho = array_sum(
    $_SESSION['carrinho']
);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Carrinho | Next Level</title>

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

            <a href="index.php">

                <i class="bi bi-grid"></i>
                Produtos

            </a>

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <a href="sair.php">Sair</a>

            <?php else: ?>

                <a href="entrar.php">Entrar</a>

            <?php endif; ?>

            <a href="carrinho.php">

                <i class="bi bi-cart3"></i>

                Carrinho (<?= $quantidadeCarrinho ?>)

            </a>

        </nav>

    </div>

</header>

<main class="container py-5">

    <a href="index.php" class="voltar-loja">

        <i class="bi bi-arrow-left"></i>
        Continuar comprando

    </a>

    <h1 class="titulo-catalogo mt-4">

        Meu carrinho

    </h1>

    <p class="contagem mb-4">

        <?= $quantidadeCarrinho ?> unidades no carrinho

    </p>

    <?php if ($mensagem !== ""): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>

    <?php if (empty($itens)): ?>

        <div class="carrinho-vazio">

            <i class="bi bi-cart3"></i>

            <h2>Seu carrinho está vazio</h2>

            <p>Adicione produtos para continuar sua compra.</p>

            <a href="index.php" class="btn-next">

                Explorar produtos

            </a>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <!-- Produtos do carrinho -->

            <div class="col-lg-8">

                <?php foreach ($itens as $item): ?>

                    <div class="item-carrinho">

                        <div class="row g-3 align-items-center">

                            <div class="col-md-3">

                                <img src="imagens/<?= htmlspecialchars($item['imagem']) ?>"
                                     alt="<?= htmlspecialchars($item['nome']) ?>"
                                     class="foto-carrinho">

                            </div>

                            <div class="col-md-5">

                                <a href="detalhes.php?id=<?= $item['id'] ?>"
                                   class="nome-carrinho">

                                    <?= htmlspecialchars($item['nome']) ?>

                                </a>

                                <p class="valor-unitario">

                                    R$ <?= number_format($item['preco'], 2, ',', '.') ?>

                                    por unidade

                                </p>

                                <p class="contagem">

                                    Estoque: <?= $item['estoque'] ?>

                                </p>

                                <?php if ($item['quantidade'] > $item['estoque']): ?>

                                    <p class="sem-estoque">

                                        Ajuste a quantidade disponível.

                                    </p>

                                <?php endif; ?>

                                <form method="POST">

                                    <input type="hidden"
                                           name="produto_id"
                                           value="<?= $item['id'] ?>">

                                    <button type="submit"
                                            name="remover"
                                            class="btn-remover">

                                        <i class="bi bi-trash"></i>

                                        Remover

                                    </button>

                                </form>

                            </div>

                            <div class="col-md-4">

                                <?php if ($item['estoque'] > 0): ?>

                                    <form method="POST">

                                        <input type="hidden"
                                               name="produto_id"
                                               value="<?= $item['id'] ?>">

                                        <label class="form-label">

                                            Quantidade

                                        </label>

                                        <div class="d-flex gap-2">

                                            <input type="number"
                                                   name="quantidade"
                                                   class="campo-quantidade"
                                                   value="<?= $item['quantidade'] ?>"
                                                   min="1"
                                                   max="<?= $item['estoque'] ?>"
                                                   required>

                                            <button type="submit"
                                                    name="atualizar"
                                                    class="btn-atualizar">

                                                Atualizar

                                            </button>

                                        </div>

                                    </form>

                                <?php endif; ?>

                                <p class="subtotal-carrinho">

                                    R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>

                                </p>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Resumo da compra -->

            <div class="col-lg-4">

                <div class="resumo-compra">

                    <h2>Resumo da compra</h2>

                    <div class="linha-resumo">

                        <span>Quantidade</span>

                        <span><?= $quantidadeCarrinho ?></span>

                    </div>

                    <div class="linha-resumo">

                        <span>Subtotal</span>

                        <span>

                            R$ <?= number_format($total, 2, ',', '.') ?>

                        </span>

                    </div>

                    <hr>

                    <div class="linha-resumo total-compra">

                        <span>Total</span>

                        <strong>

                            R$ <?= number_format($total, 2, ',', '.') ?>

                        </strong>

                    </div>

                    <?php if ($estoqueValido): ?>

                        <a href="checkout.php"
                           class="btn-next d-block text-center mt-4">

                            Finalizar compra

                            <i class="bi bi-arrow-right"></i>

                        </a>

                    <?php else: ?>

                        <button class="btn btn-secondary w-100 mt-4"
                                disabled>

                            Ajuste o estoque

                        </button>

                    <?php endif; ?>

                    <a href="index.php"
                       class="voltar-loja d-block text-center mt-3">

                        Continuar comprando

                    </a>

                </div>

            </div>

        </div>

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