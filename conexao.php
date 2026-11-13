<?php
$host = 'localhost';
$banco = 'assistencia_tecnica';
$usuario = 'root';
$senha = '';

$pdo = new PDO(
    "mysql:host=$host;dbname=$banco;charset=utf8mb4",
    $usuario,
    $senha,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
