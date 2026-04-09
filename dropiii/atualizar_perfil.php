<?php
session_start();
require_once 'conexao.php'; // Puxa a sua conexão com o banco

// Verifica se a pessoa está logada por segurança
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega os dados do formulário e tira os espaços em branco
    $id_usuario = $_SESSION['id_usuario'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // Verificação de segurança: não deixar nome ou e-mail vazios
    if (empty($nome) || empty($email)) {
        echo "<script>alert('O Nome e o E-mail são obrigatórios!'); window.history.back();</script>";
        exit();
    }

    // LÓGICA 1: Se o usuário preencheu o campo de nova senha
    if (!empty($nova_senha)) {
        // Verifica se a senha e a confirmação são iguais
        if ($nova_senha !== $confirma_senha) {
            echo "<script>alert('As senhas não coincidem. Tente novamente!'); window.history.back();</script>";
            exit();
        }
        
        // Criptografa a nova senha para segurança!
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Prepara o código para atualizar Nome, Email E Senha
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $email, $senha_hash, $id_usuario);
    } 
    // LÓGICA 2: Se o usuário NÃO preencheu a senha (só quer mudar nome/email)
    else {
        // Atualiza apenas o Nome e o Email
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $email, $id_usuario);
    }

    // Executa a atualização no banco de dados
    if ($stmt->execute()) {
        
        // MUITO IMPORTANTE: Atualiza o nome na sessão para a foto e o cabeçalho mudarem na mesma hora!
        $_SESSION['nome_usuario'] = $nome;

        echo "<script>
                alert('Seu perfil foi atualizado com sucesso! 🎉'); 
                window.location.href='index.php#client-settings';
              </script>";
    } else {
        echo "<script>alert('Erro ao atualizar o perfil. Tente novamente mais tarde.'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>