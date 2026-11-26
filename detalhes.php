<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

$id = (int) ($_GET['id'] ?? 0);

$sql = "SELECT produtos.*, categorias.nome AS categoria
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        WHERE produtos.id = ?";

$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);

$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: index.php");
    exit;
}

$quantidadeCarrinho = array_sum(
    $_SESSION['carrinho'] ?? []
);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($produto['nome']) ?> | Next Level</title>

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

                <a href="entrar.php">
                    <i class="bi bi-person"></i>
                    Entrar
                </a>

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
        Voltar aos produtos

    </a>

    <div class="painel-detalhes mt-4">

        <div class="row g-4 align-items-center">

            <div class="col-md-6">

                <img src="imagens/<?= htmlspecialchars($produto['imagem']) ?>"
                     alt="<?= htmlspecialchars($produto['nome']) ?>"
                     class="foto-detalhes">

            </div>

            <div class="col-md-6">

                <div class="conteudo-detalhes">

                    <span class="categoria-produto">

                        <?= htmlspecialchars($produto['categoria']) ?>

                    </span>

                    <h1 class="titulo-detalhes">

                        <?= htmlspecialchars($produto['nome']) ?>

                    </h1>

                    <p class="descricao-detalhes">

                        <?= htmlspecialchars($produto['descricao']) ?>

                    </p>

                    <div class="preco-detalhes">

                        R$ <?= number_format($produto['preco'], 2, ',', '.') ?>

                    </div>

                    <?php if ($produto['estoque'] > 0): ?>

                        <p class="disponibilidade">

                            <i class="bi bi-check-circle"></i>

                            <?= $produto['estoque'] ?> unidades disponíveis

                        </p>

                        <form action="carrinho.php" method="POST">

                            <input type="hidden"
                                   name="produto_id"
                                   value="<?= $produto['id'] ?>">

                            <label for="quantidade" class="mb-2">

                                Quantidade

                            </label>

                            <input type="number"
                                   name="quantidade"
                                   id="quantidade"
                                   class="campo-quantidade"
                                   value="1"
                                   min="1"
                                   max="<?= $produto['estoque'] ?>"
                                   required>

                            <button type="submit"
                                    name="adicionar"
                                    class="btn-next w-100 mt-4">

                                <i class="bi bi-cart-plus"></i>

                                Adicionar ao carrinho

                            </button>

                        </form>

                    <?php else: ?>

                        <p class="sem-estoque">

                            Produto indisponível no momento.

                        </p>

                        <button class="btn btn-secondary w-100"
                                disabled>

                            Sem estoque

                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

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