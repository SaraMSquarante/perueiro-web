<?php
session_start();
require_once 'conexao.php'; // Puxa a sua conexão com o banco

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha_pura = $_POST['senha'];
    
    // Verifica se é motorista
    if (isset($_POST['is_driver']) && $_POST['is_driver'] == 'sim') {
        $tipo = 'driver';
        $modelo = $_POST['modelo_veiculo'];
        $placa = $_POST['placa'];
    } else {
        $tipo = 'client';
        $modelo = NULL;
        $placa = NULL;
    }

    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, cpf, telefone, email, senha, tipo_usuario, modelo_veiculo, placa) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nome, $cpf, $telefone, $email, $senha_hash, $tipo, $modelo, $placa);

    if ($stmt->execute()) {
      echo "<script>alert('Conta criada com sucesso!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar. Verifique se o e-mail ou CPF já existem.'); window.history.back();</script>";
    }
    
    $stmt->close();
}
$conn->close();
?>