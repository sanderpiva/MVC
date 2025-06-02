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
// A variável $conexao DEVE ser definida neste arquivo (ex: $conexao = new PDO(...);)
require_once __DIR__ . '/config/conexao.php';

// Inclui os arquivos dos controladores. Em aplicações maiores, um autoloader seria usado.
require_once __DIR__ . '/controllers/Auth_controller.php';
require_once __DIR__ . '/controllers/Dashboard_controller.php';
require_once __DIR__ . '/controllers/Professor_controller.php';
require_once __DIR__ . '/controllers/Aluno_controller.php';
require_once __DIR__ . '/controllers/Turma_controller.php';
// require_once __DIR__ . '/controllers/Conteudo_controller.php'; // Remova o comentário se for usar

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
if (!class_exists($controllerClassName)) {
    displayErrorPage("Controller '$controllerClassName' não encontrado no sistema.", 'index.php?controller=auth&action=showLoginForm');
}

// Instancia o controlador
$controller = null; // Inicializa a variável do controlador

// Verifica qual controlador está sendo instanciado e passa a conexão se necessário.
// Esta é a parte CRÍTICA para resolver o erro 'Too few arguments'.
// Você precisa listar AQUI todos os controladores que esperam $conexao em seus construtores.
if (
    $controllerClassName === 'Turma_controller' ||
    $controllerClassName === 'Professor_controller' || // Adicione se Professor_controller precisa de $conexao
    $controllerClassName === 'Aluno_controller' ||     // Adicione se Aluno_controller precisa de $conexao
    $controllerClassName === 'Conteudo_controller'     // Adicione se Conteudo_controller precisa de $conexao
    // Adicione outros controladores aqui conforme a necessidade de $conexao
) {
    // A variável $conexao deve ter sido definida no arquivo 'config/conexao.php'.
    // Certifique-se de que ela está acessível neste escopo.
    // Ex: $conexao = new PDO("mysql:host=...", "user", "pass");
    $controller = new $controllerClassName($conexao); // Passa a conexão aqui!
} else {
    // Para controladores que não precisam da conexão no construtor (como Auth_controller, Dashboard_controller)
    $controller = new $controllerClassName();
}

// Determina o método a ser chamado no controlador
// O método que será chamado no controlador (ex: 'listTurmas', 'showCreateForm', 'handleCreatePost')
$methodToCall = $actionParam;

// Verifica se o método existe no controlador instanciado e chama-o
if (method_exists($controller, $methodToCall)) {
    // Lógica para chamar o método, passando parâmetros se necessário (como 'id' para edit/delete)
    switch ($methodToCall) {
        case 'showEditForm': // Ação para exibir formulário de edição (GET)
        case 'deleteTurma':  // Ação para excluir (GET)
            $controller->$methodToCall($_GET['id'] ?? null); // Passa o ID se for edição ou exclusão
            break;

        case 'create': // Ação 'create' via GET para exibir o formulário de criação
            // Para casos onde 'action=create' (GET) mostra o formulário, e 'action=create' (POST) processa
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Se a requisição é POST, chama o método para processar a criação
                // Assumimos que o método POST de criação é 'handleCreatePost'
                if (method_exists($controller, 'handleCreatePost')) {
                    $controller->handleCreatePost($_POST);
                } else {
                    displayErrorPage("Ação POST para 'create' não implementada no controller '$controllerClassName'.", 'index.php?controller=' . $controllerParam . '&action=list');
                }
            } else {
                // Se a requisição é GET, chama o método para exibir o formulário de criação
                $controller->showCreateForm(); // Assumimos que o método GET de criação é 'showCreateForm'
            }
            break;

        case 'update': // Ação 'update' via POST para processar atualização
            // A ação 'update' geralmente é apenas para submissões POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Se a requisição é POST, chama o método para processar a atualização
                // Assumimos que o método POST de atualização é 'handleUpdatePost'
                if (method_exists($controller, 'handleUpdatePost')) {
                    $controller->handleUpdatePost($_POST);
                } else {
                    displayErrorPage("Ação POST para 'update' não implementada no controller '$controllerClassName'.", 'index.php?controller=' . $controllerParam . '&action=list');
                }
            } else {
                // Se a requisição não for POST, redireciona ou exibe erro, pois 'update' é para POST
                displayErrorPage("Ação 'update' só pode ser acessada via POST.", 'index.php?controller=' . $controllerParam . '&action=list');
            }
            break;
            
        case 'login': // Ação 'login' no Auth_controller
        case 'registerProfessor': // Ação 'registerProfessor' no Auth_controller
        case 'registerAluno': // Ação 'registerAluno' no Auth_controller
            // Para estas ações, é importante verificar o método da requisição (GET/POST)
            // e chamar o método correto no controlador.
            // O Auth_controller já tem essa lógica interna em seus métodos login/registerProfessor.
            // Então, podemos simplesmente chamar o método, e ele lidará com $_SERVER['REQUEST_METHOD'].
            $controller->$methodToCall();
            break;

        default:
            // Para todas as outras ações que não exigem parâmetros especiais
            // (ex: 'listTurmas', 'showLoginForm', 'logout', 'showProfessorRegisterForm', 'showAlunoRegisterForm', etc.)
            $controller->$methodToCall();
            break;
    }
} else {
    // Se o método da ação não existir no controlador
    // Tenta chamar um método 'defaultAction' se existir, caso contrário exibe erro genérico
    if (method_exists($controller, 'defaultAction')) {
        $controller->defaultAction();
    } else {
        displayErrorPage("Ação '$actionParam' não encontrada no controller '$controllerClassName'.", 'index.php?controller=auth&action=showLoginForm');
    }
}

?>