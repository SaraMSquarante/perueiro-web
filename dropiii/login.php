<?php
session_start();
require_once 'conexao.php'; // Puxa a sua conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha']; // A senha que o usuário digitou na tela

    // Vai no banco de dados e procura se existe alguém com esse e-mail
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Se encontrou 1 usuário com esse e-mail...
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // Magia do PHP: Compara a senha digitada com a senha criptografada do banco!
        if (password_verify($senha_digitada, $usuario['senha'])) {
            
            // Login deu certo! Guarda os dados do usuário na "Sessão"
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome_usuario'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
            
            // VERIFICA QUEM É O USUÁRIO com base no seu banco de dados
            if ($usuario['tipo_usuario'] == 'driver') { 
                // Se for motorista, manda o sinal logado=perueiro E a âncora #dash-driver (ajuste se o id da seção for diferente)
                echo "<script>alert('Bem-vindo, Motorista " . $usuario['nome'] . "!'); window.location.href='index.php?logado=perueiro#dash-driver';</script>";
            } else {
                // Se for cliente comum, manda o sinal logado=cliente E a âncora #dash-client
                echo "<script>alert('Bem-vindo, " . $usuario['nome'] . "!'); window.location.href='index.php?logado=cliente#dash-client';</script>";
            }

        } else {
            // LOGIN DEU ERRADO (Senha incorreta) - Retorna o aviso certinho!
            echo "<script>alert('Senha incorreta! Tente novamente.'); window.history.back();</script>";
        }

    } else {
        // Se o e-mail não existir no banco
        echo "<script>alert('E-mail não encontrado! Faça seu cadastro primeiro.'); window.history.back();</script>";
    }

    $stmt->close();
}
$conn->close();
?>