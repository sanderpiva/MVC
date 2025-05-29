<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>



<?php
// index.php

// Inicia a sessão para todas as requisições, fundamental para controle de acesso
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclui os arquivos dos controladores para que suas classes estejam disponíveis.
// ESTES SÃO OS NOMES DOS ARQUIVOS REAIS!
require_once __DIR__ . '/controllers/Auth_controller.php';
require_once __DIR__ . '/controllers/Dashboard_controller.php';
// Adicione aqui outros controladores conforme o projeto cresce, por exemplo:
// require_once __DIR__ . '/controllers/turma-controller.php';
// require_once __DIR__ . '/controllers/aluno-controller.php';


// --- Função Auxiliar de Roteamento ---
// Esta função encapsula a lógica de chamar o Controller e a ação.
function dispatchRoute($controllerClassName, $actionMethod, $postActionMethod = null) {
    // Apenas instanciamos o Controller, pois sabemos que a classe já foi carregada.
    $controller = new $controllerClassName(); // Instancia o Controller usando o NOME DA CLASSE

    // Decide qual método chamar: se for POST e houver um método POST específico, usa-o.
    // Caso contrário, usa o método GET/padrão.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $postActionMethod && method_exists($controller, $postActionMethod)) {
        $controller->$postActionMethod();
    } elseif (method_exists($controller, $actionMethod)) {
        $controller->$actionMethod();
    } else {
        // Se a ação não existir no Controller, trata como erro
        displayErrorPage("Ação '$actionMethod' ou '$postActionMethod' não encontrada na classe '$controllerClassName'.", '/');
    }
}

// --- Funções Auxiliares (mantidas no index.php conforme a restrição) ---

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
    // As variáveis $errorMessage e $homeUrl serão acessíveis na view de erro
    $errorMessage = $message;
    $homeUrl = $homeUrl;
    require __DIR__ . '/views/auth/error.php';
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
// Pega a URI da requisição (ex: /login, /cadastro-professor)
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');

// Calcula o caminho base da aplicação (ex: /php_if/MVC-Nsistema_gerenciamento_academico)
// Isso é necessário porque a aplicação está em um subdiretório.
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Se a URL for http://localhost/php_if/MVC-Nsistema_gerenciamento_academico/index.php
// $_SERVER['SCRIPT_NAME'] será /php_if/MVC-Nsistema_gerenciamento_academico/index.php
// dirname() resultará em /php_if/MVC-Nsistema_gerenciamento_academico

// Remove o basePath do requestUri para obter o caminho limpo da rota
$path = substr($requestUri, strlen($basePath));
// Remove barras iniciais/finais para padronizar o path
$path = trim($path, '/');

// Se o path resultante for 'index.php', transformamos em string vazia para a rota raiz
if ($path === 'index.php') {
    $path = '';
}


// Mapeamento de rotas:
// Cada entrada no array define:
// 'url_caminho' => [ 'NomeDaClasseDoController', 'metodoParaGET', 'metodoParaPOST' (opcional) ]
// O 'NomeDaClasseDoController' deve ser o nome REAL da classe dentro do arquivo PHP
// (ex: AuthController, mesmo que o arquivo seja auth-controller.php)
$routes = [
    ''                              => ['Auth_controller',    'showLoginForm'], // Rota para a raiz (página inicial, formulário de login)
    'login'                         => ['Auth_controller',    'showLoginForm', 'login'], // Rota para o login
    'logout'                        => ['Auth_controller',    'logout'],
    'cadastro-professor'            => ['Auth_controller',    'showProfessorRegisterForm', 'registerProfessor'],
    'cadastro-aluno'                => ['Auth_controller',    'showAlunoRegisterForm',     'registerAluno'],

    // Rotas protegidas (exigem login)
    'Professor_dashboard'           => ['Dashboard_controller', 'showProfessorDashboard'],
    'Aluno_selecao_atividade'       => ['Dashboard_controller', 'showAlunoSelection',      'handleAlunoActivitySelection'],
    'Aluno_dashboard_dinamica'      => ['Dashboard_controller', 'showAlunoDynamicDashboard'],
    'Aluno_dashboard_algebrando'    => ['Dashboard_controller', 'showAlunoAlgebrandoDashboard'],

    // --- Exemplos para CRUDs futuros ---
    // Se você tiver um arquivo `controllers/turma-controller.php` com uma classe `TurmaController`:
    // 'turma-cadastrar'             => ['TurmaController',    'showCreateForm', 'create'],
    // 'turma-consultar'             => ['TurmaController',    'listAll'],
];

// ----------------------------------------------------
// Lógica para despachar a requisição para o Controller e método corretos
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