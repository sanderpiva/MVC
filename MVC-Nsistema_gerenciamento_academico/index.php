<?php
// Ativa a exibição de erros para depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicia a sessão para todas as requisições
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclui o arquivo de conexão com o banco de dados (se necessário para todos os controllers)
// Ou pode ser injetado nos modelos/controllers quando necessário.
require_once __DIR__ . '/config/conexao.php'; // Verifique o caminho correto

// Inclui os arquivos dos controladores para que suas classes estejam disponíveis.
require_once __DIR__ . '/controllers/Auth_controller.php';
require_once __DIR__ . '/controllers/Dashboard_controller.php';
// Adicione aqui outros controladores conforme o projeto cresce

// --- Função Auxiliar de Roteamento ---
function dispatchRoute($controllerClassName, $actionMethod, $postActionMethod = null) {
    global $conexao; // Garante que a conexão esteja disponível se o controller precisar dela

    // Instancia o Controller, passando a conexão se ele precisar
    $controller = new $controllerClassName();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $postActionMethod && method_exists($controller, $postActionMethod)) {
        $controller->$postActionMethod();
    } elseif (method_exists($controller, $actionMethod)) {
        $controller->$actionMethod();
    } else {
        displayErrorPage("Ação '$actionMethod' ou '$postActionMethod' não encontrada na classe '$controllerClassName'.", '/');
    }
}

// --- Funções Auxiliares Globais (para redirecionamento e erro) ---
// Elas são mantidas aqui no index.php, ou você pode movê-las para um arquivo de "helpers"

/**
 * Redireciona o navegador para uma nova URL.
 * @param string $url A URL para redirecionar.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Exibe uma página de erro formatada.
 * @param string $message A mensagem de erro a ser exibida.
 * @param string $homeUrl A URL para o botão "Voltar para a Home".
 */
function displayErrorPage($message, $homeUrl = '/') {
    $errorMessage = $message;
    $homeUrl = $homeUrl;
    require __DIR__ . '/views/auth/error.php'; // Verifique o caminho da sua view de erro
    exit();
}

/**
 * Verifica se o usuário está autenticado e, opcionalmente, se é de um tipo específico.
 * Se não, redireciona ou exibe página de erro.
 * @param string|null $userType O tipo de usuário esperado ('professor' ou 'aluno'). Se null, apenas verifica se está logado.
 */
function requireAuth($userType = null) {
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        redirect('/'); // Não logado, volta para a home
    }
    if ($userType && $_SESSION['tipo_usuario'] !== $userType) {
        displayErrorPage("Acesso negado. Você não tem permissão para acessar esta página.", '/');
    }
}

// --- Lógica de Roteamento ---
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$path = substr($requestUri, strlen($basePath));
$path = trim($path, '/');

if ($path === 'index.php' || $path === '') {
    $path = ''; // Rota raiz
}

// Mapeamento de rotas:
// 'url_caminho' => [ 'NomeDaClasseDoController', 'metodoParaGET', 'metodoParaPOST' (opcional) ]
$routes = [
    ''                    => ['Auth_controller', 'showLoginForm'], // Rota para a raiz (página inicial, formulário de login)
    'login'               => ['Auth_controller', 'showLoginForm', 'login'], // Processa o POST de login
    'logout'              => ['Auth_controller', 'logout'],
    'cadastro-professor'  => ['Auth_controller', 'showProfessorRegisterForm', 'registerProfessor'],
    'cadastro-aluno'      => ['Auth_controller', 'showAlunoRegisterForm', 'registerAluno'],

    // Rotas protegidas (exigem login, e a verificação é feita dentro do controller)
    'professor-dashboard' => ['Dashboard_controller', 'showProfessorDashboard'],
    'aluno-selecao-atividade' => ['Dashboard_controller', 'showAlunoSelection', 'handleAlunoActivitySelection'],
    'aluno-dashboard-dinamica' => ['Dashboard_controller', 'showAlunoDynamicDashboard'],
    'aluno-dashboard-algebrando' => ['Dashboard_controller', 'showAlunoAlgebrandoDashboard'],

    // Exemplo de rota para gerenciar professores (GET para exibir lista, POST para criar novo)
    // 'professor-gerenciar' => ['Professor_controller', 'listProfessors', 'createProfessor'],
    // 'professor-editar'    => ['Professor_controller', 'showEditForm', 'updateProfessor'],
    // 'professor-excluir'   => ['Professor_controller', 'deleteProfessor'],
];

// ----------------------------------------------------
// Despacho da requisição para o Controller e método corretos
// ----------------------------------------------------
if (array_key_exists($path, $routes)) {
    $routeInfo = $routes[$path];
    $controllerClassName = $routeInfo[0];
    $getOrDefaultAction = $routeInfo[1];
    $postAction = $routeInfo[2] ?? null;

    dispatchRoute($controllerClassName, $getOrDefaultAction, $postAction);

} else {
    // Se a rota não for encontrada no mapeamento
    displayErrorPage("Página não encontrada.", '/');
}
?>