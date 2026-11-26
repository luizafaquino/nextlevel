<?php

session_name("nextlevel_session");
session_start();

require_once "../configuracao/banco.php";

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {

    header("Location: ../entrar.php");
    exit;

}

if (empty($_SESSION['token'])) {

    $_SESSION['token'] = bin2hex(random_bytes(32));

}

$mensagem = "";
$erro = "";

$categorias = $conexao->query(
    "SELECT * FROM categorias ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

// Cadastrar, editar ou excluir

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals(
        $_SESSION['token'],
        $_POST['token'] ?? ''
    )) {

        die("Solicitação inválida.");

    }

    if (isset($_POST['salvar'])) {

        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = (int) ($_POST['categoria_id'] ?? 0);
        $preco = filter_var(
            $_POST['preco'] ?? '',
            FILTER_VALIDATE_FLOAT
        );
        $estoque = filter_var(
            $_POST['estoque'] ?? '',
            FILTER_VALIDATE_INT
        );
        $imagem = basename(trim($_POST['imagem'] ?? ''));

        if (
            $nome === '' ||
            $descricao === '' ||
            $categoria <= 0 ||
            $preco === false ||
            $preco <= 0 ||
            $estoque === false ||
            $estoque < 0 ||
            $imagem === '' ||
            !preg_match('/\.(jpg|jpeg|png|webp)$/i', $imagem)
        ) {

            $erro = "Preencha todos os campos corretamente.";

        } else {

            try {

                if ($id > 0) {

                    $sql = "UPDATE produtos SET
                            nome = ?,
                            descricao = ?,
                            categoria_id = ?,
                            preco = ?,
                            estoque = ?,
                            imagem = ?
                            WHERE id = ?";

                    $consulta = $conexao->prepare($sql);

                    $consulta->execute([
                        $nome,
                        $descricao,
                        $categoria,
                        $preco,
                        $estoque,
                        $imagem,
                        $id
                    ]);

                    header("Location: gerenciar.php?status=editado");
                    exit;

                } else {

                    $sql = "INSERT INTO produtos
                            (nome, descricao, categoria_id,
                             preco, estoque, imagem)
                            VALUES (?, ?, ?, ?, ?, ?)";

                    $consulta = $conexao->prepare($sql);

                    $consulta->execute([
                        $nome,
                        $descricao,
                        $categoria,
                        $preco,
                        $estoque,
                        $imagem
                    ]);

                    header("Location: gerenciar.php?status=cadastrado");
                    exit;

                }

            } catch (PDOException $e) {

                $erro = "Não foi possível salvar o produto.";

            }

        }

    }

    // Excluir produto

    if (isset($_POST['excluir'])) {

        $id = (int) $_POST['id'];

        try {

            $consulta = $conexao->prepare(
                "DELETE FROM produtos WHERE id = ?"
            );

            $consulta->execute([$id]);

            header("Location: gerenciar.php?status=excluido");
            exit;

        } catch (PDOException $e) {

            $erro = "Não foi possível excluir o produto.
                     Ele pode estar vinculado a um pedido.";

        }

    }

}

// Mensagens

$status = $_GET['status'] ?? '';

if ($status === 'cadastrado') {

    $mensagem = "Produto cadastrado com sucesso!";

} elseif ($status === 'editado') {

    $mensagem = "Produto atualizado com sucesso!";

} elseif ($status === 'excluido') {

    $mensagem = "Produto excluído com sucesso!";

}

// Buscar produto para edição

$editar = null;

if (isset($_GET['editar'])) {

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([
        (int) $_GET['editar']
    ]);

    $editar = $consulta->fetch(PDO::FETCH_ASSOC);

}

// Listar produtos

$sql = "SELECT produtos.*, categorias.nome AS categoria
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        ORDER BY produtos.id";

$produtos = $conexao->query($sql)
    ->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Gerenciar produtos | Next Level</title>

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

            <a href="vendas.php">Pedidos</a>

            <a href="../index.php">Ver loja</a>

            <a href="../sair.php">Sair</a>

        </nav>

    </div>

</header>

<main class="container py-5">

    <a href="index.php" class="voltar-loja">

        ← Voltar ao painel

    </a>

    <h1 class="titulo-catalogo mt-4 mb-4">

        Gerenciar produtos

    </h1>

    <?php if ($mensagem !== ''): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>

    <?php if ($erro !== ''): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>

    <div class="bloco-admin mb-5">

        <h2>

            <?= $editar ? 'Editar produto' : 'Cadastrar produto' ?>

        </h2>

        <form method="POST">

            <input type="hidden"
                   name="token"
                   value="<?= $_SESSION['token'] ?>">

            <input type="hidden"
                   name="id"
                   value="<?= $editar['id'] ?? 0 ?>">

            <div class="row g-3">

                <div class="col-md-8">

                    <label class="form-label">Nome do produto</label>

                    <input type="text"
                           name="nome"
                           class="campo-conta"
                           maxlength="150"
                           value="<?= htmlspecialchars($editar['nome'] ?? '') ?>"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">Categoria</label>

                    <select name="categoria_id"
                            class="campo-conta"
                            required>

                        <option value="">Selecione</option>

                        <?php foreach ($categorias as $categoria): ?>

                            <option value="<?= $categoria['id'] ?>"
                                <?= ($editar['categoria_id'] ?? 0) == $categoria['id'] ? 'selected' : '' ?>>

                                <?= htmlspecialchars($categoria['nome']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-12">

                    <label class="form-label">Descrição</label>

                    <textarea name="descricao"
                              class="campo-conta"
                              rows="4"
                              required><?= htmlspecialchars($editar['descricao'] ?? '') ?></textarea>

                </div>

                <div class="col-md-4">

                    <label class="form-label">Preço</label>

                    <input type="number"
                           name="preco"
                           class="campo-conta"
                           step="0.01"
                           min="0.01"
                           value="<?= $editar['preco'] ?? '' ?>"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">Estoque</label>

                    <input type="number"
                           name="estoque"
                           class="campo-conta"
                           min="0"
                           value="<?= $editar['estoque'] ?? '' ?>"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">Nome da imagem</label>

                    <input type="text"
                           name="imagem"
                           class="campo-conta"
                           placeholder="produto.jpg"
                           value="<?= htmlspecialchars($editar['imagem'] ?? '') ?>"
                           required>

                </div>

            </div>

            <button type="submit"
                    name="salvar"
                    class="btn-next mt-4">

                <?= $editar ? 'Salvar alterações' : 'Cadastrar produto' ?>

            </button>

            <?php if ($editar): ?>

                <a href="gerenciar.php"
                   class="voltar-loja ms-3">

                    Cancelar edição

                </a>

            <?php endif; ?>

        </form>

    </div>

    <h2 class="mb-4">Produtos cadastrados</h2>

    <div class="table-responsive">

        <table class="table table-dark table-hover
                      align-middle tabela-admin">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($produtos as $produto): ?>

                    <tr>

                        <td><?= $produto['id'] ?></td>

                        <td>

                            <img src="../imagens/<?= htmlspecialchars($produto['imagem']) ?>"
                                 alt="<?= htmlspecialchars($produto['nome']) ?>"
                                 class="foto-admin">

                        </td>

                        <td>

                            <?= htmlspecialchars($produto['nome']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($produto['categoria']) ?>

                        </td>

                        <td class="valor-admin">

                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>

                        </td>

                        <td><?= $produto['estoque'] ?></td>

                        <td>

                            <div class="d-flex gap-2">

                                <a href="gerenciar.php?editar=<?= $produto['id'] ?>"
                                   class="btn-editar">

                                    <i class="bi bi-pencil"></i>

                                    Editar

                                </a>

                                <form method="POST"
                                      class="form-excluir">

                                    <input type="hidden"
                                           name="token"
                                           value="<?= $_SESSION['token'] ?>">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= $produto['id'] ?>">

                                    <button type="submit"
                                            name="excluir"
                                            class="btn-excluir">

                                        <i class="bi bi-trash"></i>

                                        Excluir

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

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