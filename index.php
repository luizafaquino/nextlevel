<?php

session_name("nextlevel_session");
session_start();

require_once "configuracao/banco.php";

$busca = trim($_GET['busca'] ?? '');
$categoria = (int) ($_GET['categoria'] ?? 0);
$ordem = $_GET['ordem'] ?? '';

$categorias = $conexao->query(
    "SELECT * FROM categorias ORDER BY id"
)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT produtos.*, categorias.nome AS categoria
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        WHERE 1 = 1";

$parametros = [];

if ($busca !== '') {
    $sql .= " AND produtos.nome LIKE ?";
    $parametros[] = "%$busca%";
}

if ($categoria > 0) {
    $sql .= " AND produtos.categoria_id = ?";
    $parametros[] = $categoria;
}

if ($ordem === 'menor') {
    $sql .= " ORDER BY produtos.preco ASC";
} elseif ($ordem === 'maior') {
    $sql .= " ORDER BY produtos.preco DESC";
} else {
    $sql .= " ORDER BY produtos.id ASC";
}

$consulta = $conexao->prepare($sql);
$consulta->execute($parametros);

$produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);

$titulo = "Todos os produtos";

foreach ($categorias as $item) {
    if ($item['id'] == $categoria) {
        $titulo = $item['nome'];
    }
}

$icones = [
    'Teclados' => 'bi-keyboard',
    'Mouses' => 'bi-mouse',
    'Headsets' => 'bi-headset',
    'Mousepads' => 'bi-square'
];

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

    <title>Next Level | Gaming Store</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="estilo.css">

</head>

<body>

<!-- Cabeçalho -->

<header class="topo">

    <div class="container-fluid px-lg-4">

        <div class="row align-items-center g-3">

            <div class="col-md-3 text-center text-md-start">

                <a href="index.php" class="marca">
                    NEXT <span>LEVEL</span>
                </a>

            </div>

            <div class="col-md-5">

                <form method="GET"
                      action="index.php"
                      class="pesquisa">

                    <input type="text"
                           name="busca"
                           class="campo-busca"
                           placeholder="Buscar produtos..."
                           value="<?= htmlspecialchars($busca) ?>">

                    <input type="hidden"
                           name="categoria"
                           value="<?= $categoria ?>">

                    <button class="btn-next" type="submit">

                        <i class="bi bi-search"></i>

                    </button>

                </form>

            </div>

            <div class="col-md-4">

                <nav class="links-topo">

                    <?php if (isset($_SESSION['usuario_id'])): ?>

                        <?php if (($_SESSION['usuario_tipo'] ?? '') === 'admin'): ?>

                            <a href="painel/index.php">
                                <i class="bi bi-gear"></i>
                                Painel
                            </a>

                        <?php endif; ?>

                        <a href="sair.php">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
                        </a>

                    <?php else: ?>

                        <a href="entrar.php">
                            <i class="bi bi-person"></i>
                            Entrar
                        </a>

                        <a href="registrar.php">
                            Cadastrar
                        </a>

                    <?php endif; ?>

                    <a href="carrinho.php">

                        <i class="bi bi-cart3"></i>

                        Carrinho (<?= $quantidadeCarrinho ?>)

                    </a>

                </nav>

            </div>

        </div>

    </div>

</header>

<!-- Área principal -->

<main class="area-loja">

    <div class="row g-0">

        <!-- Menu lateral -->

        <aside class="col-md-3 col-lg-2 menu-lateral">

            <h2>CATEGORIAS</h2>

            <a href="index.php"
               class="link-categoria <?= $categoria === 0 ? 'ativo' : '' ?>">

                <i class="bi bi-grid"></i>

                Todos os produtos

            </a>

            <?php foreach ($categorias as $item): ?>

                <a href="index.php?categoria=<?= $item['id'] ?>"
                   class="link-categoria <?= $categoria == $item['id'] ? 'ativo' : '' ?>">

                    <i class="bi <?= $icones[$item['nome']] ?? 'bi-box' ?>"></i>

                    <?= htmlspecialchars($item['nome']) ?>

                </a>

            <?php endforeach; ?>

            <hr>

            <h2>MINHA CONTA</h2>

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <p class="text-light">

                    Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!

                </p>

                <a href="sair.php" class="link-categoria">

                    <i class="bi bi-box-arrow-right"></i>

                    Sair da conta

                </a>

            <?php else: ?>

                <a href="entrar.php" class="link-categoria">

                    <i class="bi bi-person"></i>

                    Entrar

                </a>

                <a href="registrar.php" class="link-categoria">

                    <i class="bi bi-person-plus"></i>

                    Criar conta

                </a>

            <?php endif; ?>

        </aside>

        <!-- Catálogo -->

        <section class="col-md-9 col-lg-10 catalogo">

            <div class="d-flex flex-wrap
                        justify-content-between
                        align-items-center gap-3 mb-4">

                <div>

                    <h1 class="titulo-catalogo">

                        <?= htmlspecialchars($titulo) ?>

                    </h1>

                    <span class="contagem">

                        <?= count($produtos) ?> produtos encontrados

                    </span>

                </div>

                <form method="GET" action="index.php">

                    <input type="hidden"
                           name="categoria"
                           value="<?= $categoria ?>">

                    <input type="hidden"
                           name="busca"
                           value="<?= htmlspecialchars($busca) ?>">

                    <select name="ordem"
                            class="ordenacao"
                            id="ordenar-produtos">

                        <option value="">
                            Ordenar produtos
                        </option>

                        <option value="menor"
                            <?= $ordem === 'menor' ? 'selected' : '' ?>>

                            Menor preço

                        </option>

                        <option value="maior"
                            <?= $ordem === 'maior' ? 'selected' : '' ?>>

                            Maior preço

                        </option>

                    </select>

                    <noscript>
                        <button type="submit">Ordenar</button>
                    </noscript>

                </form>

            </div>

            <?php if ($busca !== ''): ?>

                <p class="contagem">

                    Resultado da busca por:

                    <strong>
                        <?= htmlspecialchars($busca) ?>
                    </strong>

                    <a href="index.php"
                       class="text-info ms-2">

                        Limpar busca

                    </a>

                </p>

            <?php endif; ?>

            <?php if (empty($produtos)): ?>

                <div class="sem-resultados">

                    <h4>Nenhum produto encontrado.</h4>

                    <p>Tente buscar outro produto.</p>

                    <a href="index.php" class="btn-next">

                        Ver todos os produtos

                    </a>

                </div>

            <?php else: ?>

                <div class="row g-3">

                    <?php foreach ($produtos as $produto): ?>

                        <div class="col-6 col-lg-4 col-xl-3">

                            <article class="card-gamer">

                                <img src="imagens/<?= htmlspecialchars($produto['imagem']) ?>"
                                     alt="<?= htmlspecialchars($produto['nome']) ?>"
                                     class="foto-produto">

                                <div class="info-produto">

                                    <span class="categoria-produto">

                                        <?= htmlspecialchars($produto['categoria']) ?>

                                    </span>

                                    <h2 class="nome-produto">

                                        <?= htmlspecialchars($produto['nome']) ?>

                                    </h2>

                                    <div class="preco-produto">

                                        R$ <?= number_format($produto['preco'], 2, ',', '.') ?>

                                    </div>

                                    <?php if ($produto['estoque'] > 0): ?>

                                        <span class="disponibilidade">

                                            <i class="bi bi-check-circle"></i>

                                            Disponível

                                        </span>

                                    <?php else: ?>

                                        <span class="disponibilidade sem-estoque">

                                            Indisponível

                                        </span>

                                    <?php endif; ?>

                                    <a href="detalhes.php?id=<?= $produto['id'] ?>"
                                       class="btn-next">

                                        Ver produto

                                    </a>

                                </div>

                            </article>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

<!-- Rodapé -->

<footer class="rodape">

    <strong>NEXT LEVEL</strong>

    <p class="mt-2 mb-0">

        Loja de acessórios gamers | Projeto acadêmico - 2026

    </p>

</footer>

<script src="script.js"></script>

</body>
</html>