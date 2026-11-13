
<?php
require_once 'conexao.php';

$pagina = $_GET['pagina'] ?? 'ordens';
$acao = $_GET['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $tipo = $_POST['tipo'];

    if ($tipo == 'cliente_salvar') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        if ($id > 0) {
            $sql = "UPDATE clientes SET nome=?, telefone=?, email=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $telefone, $email, $id]);
        } else {
            $sql = "INSERT INTO clientes (nome, telefone, email) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $telefone, $email]);
        }

        header('Location: index.php?pagina=clientes');
        exit;
    }

    if ($tipo == 'cliente_excluir') {
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id=?");
        $stmt->execute([$_POST['id']]);

        header('Location: index.php?pagina=clientes');
        exit;
    }

    if ($tipo == 'os_salvar') {
        $id = $_POST['id'];
        $cliente = $_POST['cliente_id'];
        $aparelho = $_POST['aparelho'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $defeito = $_POST['defeito'];
        $servico = $_POST['servico_realizado'];
        $valor = $_POST['valor'];
        $status = $_POST['status'];

        if ($id > 0) {
            $sql = "UPDATE ordens_servico SET cliente_id=?, aparelho=?, marca=?, modelo=?, defeito=?, servico_realizado=?, valor=?, status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cliente, $aparelho, $marca, $modelo, $defeito, $servico, $valor, $status, $id]);
        } else {
            $sql = "INSERT INTO ordens_servico (cliente_id, aparelho, marca, modelo, defeito, servico_realizado, valor, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cliente, $aparelho, $marca, $modelo, $defeito, $servico, $valor, $status]);
        }

        header('Location: index.php');
        exit;
    }

    if ($tipo == 'os_excluir') {
        $stmt = $pdo->prepare("DELETE FROM ordens_servico WHERE id=?");
        $stmt->execute([$_POST['id']]);

        header('Location: index.php');
        exit;
    }
}

$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nome")->fetchAll();
$clientes_lista = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();

$cliente_edicao = null;

if ($pagina == 'clientes' && $acao == 'editar') {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $cliente_edicao = $stmt->fetch();
}

$os_edicao = null;

if ($pagina == 'ordens' && $acao == 'editar') {
    $stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $os_edicao = $stmt->fetch();
}

$busca = $_GET['busca'] ?? '';

$sql = "SELECT ordens_servico.*, clientes.nome AS cliente_nome, clientes.telefone
        FROM ordens_servico
        INNER JOIN clientes ON clientes.id = ordens_servico.cliente_id";

if ($busca != '') {
    $sql .= " WHERE clientes.nome LIKE ? OR ordens_servico.aparelho LIKE ? OR ordens_servico.modelo LIKE ?";
    $termo = "%$busca%";
    $stmt = $pdo->prepare($sql . " ORDER BY ordens_servico.id DESC");
    $stmt->execute([$termo, $termo, $termo]);
} else {
    $stmt = $pdo->query($sql . " ORDER BY ordens_servico.id DESC");
}

$ordens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RB Tech Assistência</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <a href="index.php">Ordens de serviço</a>
    <a href="index.php?pagina=clientes">Clientes</a>
</nav>

<main>

<?php if ($pagina == 'clientes'): ?>

    <h2>Clientes</h2>

    <section class="bloco">
        <h2><?= $cliente_edicao ? 'Editar cliente' : 'Cadastrar cliente' ?></h2>

        <form method="POST" class="formulario">
            <input type="hidden" name="tipo" value="cliente_salvar">
            <input type="hidden" name="id" value="<?= $cliente_edicao['id'] ?? 0 ?>">

            <div class="campo">
                <label>Nome</label>
                <input name="nome" required value="<?= $cliente_edicao['nome'] ?? '' ?>">
            </div>

            <div class="campo">
                <label>Telefone</label>
                <input name="telefone" required value="<?= $cliente_edicao['telefone'] ?? '' ?>">
            </div>

            <div class="campo largo">
                <label>E-mail</label>
                <input name="email" type="email" value="<?= $cliente_edicao['email'] ?? '' ?>">
            </div>

            <button class="botao">Salvar</button>
        </form>
    </section>

    <section class="bloco">
        <h2>Clientes cadastrados</h2>

        <table>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($clientes_lista as $cliente): ?>
            <tr>
                <td><?= $cliente['nome'] ?></td>
                <td><?= $cliente['telefone'] ?></td>
                <td><?= $cliente['email'] ?></td>
                <td>
                    <a href="index.php?pagina=clientes&acao=editar&id=<?= $cliente['id'] ?>">Editar</a>

                    <form method="POST">
                        <input type="hidden" name="tipo" value="cliente_excluir">
                        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                        <button>Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>

<?php else: ?>

    <h2>Ordens de serviço</h2>

    <section class="bloco">
        <h2><?= $os_edicao ? 'Editar ordem' : 'Nova ordem de serviço' ?></h2>

        <?php if ($clientes): ?>

        <form method="POST" class="formulario">
            <input type="hidden" name="tipo" value="os_salvar">
            <input type="hidden" name="id" value="<?= $os_edicao['id'] ?? 0 ?>">

            <div class="campo largo">
                <label>Cliente</label>
                <select name="cliente_id" required>
                    <option value="">Selecione</option>

                    <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>"
                    <?= ($os_edicao['cliente_id'] ?? '') == $cliente['id'] ? 'selected' : '' ?>>
                        <?= $cliente['nome'] ?>
                    </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="campo">
                <label>Aparelho</label>
                <input name="aparelho" required value="<?= $os_edicao['aparelho'] ?? '' ?>">
            </div>

            <div class="campo">
                <label>Marca</label>
                <input name="marca" value="<?= $os_edicao['marca'] ?? '' ?>">
            </div>

            <div class="campo">
                <label>Modelo</label>
                <input name="modelo" value="<?= $os_edicao['modelo'] ?? '' ?>">
            </div>

            <div class="campo">
                <label>Valor</label>
                <input name="valor" type="number" step="0.01" value="<?= $os_edicao['valor'] ?? '0' ?>">
            </div>

            <div class="campo largo">
                <label>Defeito relatado</label>
                <textarea name="defeito" required><?= $os_edicao['defeito'] ?? '' ?></textarea>
            </div>

            <div class="campo largo">
                <label>Serviço realizado</label>
                <textarea name="servico_realizado"><?= $os_edicao['servico_realizado'] ?? '' ?></textarea>
            </div>

            <div class="campo">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['Aguardando', 'Em andamento', 'Concluído', 'Entregue'] as $status): ?>
                    <option value="<?= $status ?>"
                    <?= ($os_edicao['status'] ?? 'Aguardando') == $status ? 'selected' : '' ?>>
                        <?= $status ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="botao">Salvar ordem</button>
        </form>

        <?php else: ?>

            <p>Cadastre um cliente antes de abrir uma ordem.</p>
            <a href="index.php?pagina=clientes">Cadastrar cliente</a>

        <?php endif; ?>
    </section>

    <section class="bloco">
        <h2>Ordens cadastradas</h2>

        <form method="GET" class="busca">
            <input type="text" name="busca" placeholder="Pesquisar ordem ou cliente" value="<?= $busca ?>">
            <button class="botao">Pesquisar</button>
        </form>

        <table>
            <tr>
                <th>OS</th>
                <th>Cliente</th>
                <th>Aparelho</th>
                <th>Defeito</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($ordens as $os): ?>
            <tr>
                <td><?= $os['id'] ?></td>
                <td><?= $os['cliente_nome'] ?></td>
                <td><?= $os['aparelho'] ?> <?= $os['modelo'] ?></td>
                <td><?= $os['defeito'] ?></td>
                <td>R$ <?= number_format($os['valor'], 2, ',', '.') ?></td>
                <td><?= $os['status'] ?></td>
                <td>
                    <a href="index.php?acao=editar&id=<?= $os['id'] ?>">Editar</a>

                    <form method="POST">
                        <input type="hidden" name="tipo" value="os_excluir">
                        <input type="hidden" name="id" value="<?= $os['id'] ?>">
                        <button>Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </section>

<?php endif; ?>

</main>
</body>
</html>
