<?php
// Ativa a exibição de erros para depuração (REMOVER EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão para todas as requisições, se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Inclusão de Arquivos Essenciais ---
// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/config/conexao.php';

// Inclui os arquivos dos controladores. Em aplicações maiores, um autoloader seria usado.
require_once __DIR__ . '/controllers/Auth_controller.php';
require_once __DIR__ . '/controllers/Dashboard_controller.php';
// Adicione aqui outros controladores conforme o projeto cresce (ex: Atividades_controller.php)

// --- Funções Auxiliares Globais ---
// São funções que podem ser usadas em qualquer lugar do seu código (controladores, modelos, etc.)

/**
 * Redireciona o navegador para uma nova URL.
 * @param string $url A URL completa ou relativa para redirecionar.
 */
function redirect($url) {
    header("Location: " . $url);
    exit(); // É crucial parar a execução após um redirecionamento
}

/**
 * Exibe uma página de erro formatada com uma mensagem e um link para retornar.
 * @param string $message A mensagem de erro a ser exibida.
 * @param string $homeUrl A URL para o botão "Voltar para a Home" ou outra página.
 */
function displayErrorPage($message, $homeUrl = 'index.php?controller=auth&action=showLoginForm') {
    // Estas variáveis são disponibilizadas para a view de erro
    global $errorMessage, $homeUrlForButton;
    $errorMessage = $message;
    $homeUrlForButton = $homeUrl;
    require __DIR__ . '/views/auth/error.php'; // Caminho para sua view de erro
    exit(); // É crucial parar a execução após exibir a página de erro
}

/**
 * Verifica se o usuário está autenticado e, opcionalmente, se é de um tipo específico.
 * Se as condições não forem atendidas, redireciona ou exibe uma página de erro.
 * @param string|null $userType O tipo de usuário esperado ('professor' ou 'aluno'). Se null, apenas verifica se está logado.
 */
function requireAuth($userType = null) {
    // Se o usuário não estiver logado
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        redirect('index.php?controller=auth&action=showLoginForm'); // Redireciona para o login
    }
    // Se o tipo de usuário for especificado e não corresponder ao logado
    if ($userType && $_SESSION['tipo_usuario'] !== $userType) {
        displayErrorPage("Acesso negado. Você não tem permissão para acessar esta página.", 'index.php?controller=auth&action=showLoginForm');
    }
}

// --- Lógica de Roteamento (interpreta os parâmetros $_GET) ---

// Obtém o nome do controlador da URL, padrão para 'auth' se não especificado
$controllerParam = $_GET['controller'] ?? 'auth';
// Obtém o nome da ação (método) da URL, padrão para 'showLoginForm' se não especificado
$actionParam = $_GET['action'] ?? 'showLoginForm';

// Constrói o nome completo da classe do controlador (ex: 'auth' -> 'Auth_controller')
$controllerClassName = ucfirst($controllerParam) . '_controller';

// Verifica se o arquivo do controlador existe e se a classe foi definida
// (O require_once já foi feito no início, então class_exists é suficiente aqui)
if (!class_exists($controllerClassName)) {
    displayErrorPage("Controller '$controllerClassName' não encontrado no sistema.", 'index.php?controller=auth&action=showLoginForm');
}

// Instancia o controlador
$controller = new $controllerClassName();

// Determina o método a ser chamado no controlador
$methodToCall = $actionParam;

// Verifica se o método existe no controlador instanciado
if (method_exists($controller, $methodToCall)) {
    // Chama o método no controlador.
    // Quaisquer dados POST ou GET estarão disponíveis para o método via $_POST ou $_GET.
    $controller->$methodToCall();
} else {
    // Se o método não existir, exibe uma página de erro
    displayErrorPage("Ação '$actionParam' não encontrada no controller '$controllerClassName'.", 'index.php?controller=auth&action=showLoginForm');
}

?>