<?php

// Adjust path to conexao.php based on your new project root
//require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/Conteudo_model.php';

session_start();

// Basic authentication check
// Assuming your index.php or a central router handles initial login redirect
// If not, keep a redirect here but consider a more global authentication middleware
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'professor') {
    // Redirect to the login page or a general access denied page
    header("Location: /index.php"); // Adjust to your actual login page path
    exit();
}

class Conteudo_Controller {
    private $model;
    private $conexao; // Keep connection to pass to model

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
        $this->model = new Conteudo_Model($conexao);
    }

    // Handles displaying the form for creating or editing content
    public function showForm($id_conteudo = null) {
        $isUpdating = false;
        $conteudoData = [];
        $errors = [];
        $nomeDisciplinaAtual = '';

        if ($id_conteudo) {
            $conteudoData = $this->model->getConteudoById($id_conteudo);
            if (!$conteudoData) {
                $_SESSION['error'] = "Conteúdo com ID $id_conteudo não encontrado.";
                header("Location: /conteudo?action=list"); // Redirect to list if not found
                exit();
            }
            $isUpdating = true;
            $nomeDisciplinaAtual = htmlspecialchars($conteudoData['nomeDisciplina']);
        }

        $disciplinas = $this->model->getAllDisciplinas();
        $professores = $this->model->getAllProfessores();
        $professorsLookup = [];
        foreach ($professores as $professor) {
            $professorsLookup[$professor['id_professor']] = $professor['nome'];
        }

        // Pass data to the view
        require_once __DIR__ . '/../views/conteudo/Create_edit.php';
    }

    // Handles processing the form submission for creating or updating content
    public function processForm() {
        $data = $_POST;
        $errors = $this->validate($data);

        if (empty($errors)) {
            if (isset($data['id_conteudo']) && !empty($data['id_conteudo'])) {
                // Update operation
                if ($this->model->updateConteudo($data)) {
                    $_SESSION['message'] = "Conteúdo atualizado com sucesso!";
                    header("Location: /conteudo?action=list");
                    exit();
                } else {
                    $errors[] = "Erro ao atualizar conteúdo.";
                }
            } else {
                // Create operation
                if ($this->model->createConteudo($data)) {
                    $_SESSION['message'] = "Conteúdo cadastrado com sucesso!";
                    header("Location: /conteudo?action=list");
                    exit();
                } else {
                    $errors[] = "Erro ao cadastrar conteúdo.";
                }
            }
        }

        // If there are errors or the operation failed, reload the form with existing data and errors
        $isUpdating = isset($data['id_conteudo']) && !empty($data['id_conteudo']);
        $conteudoData = $data; // Repopulate form with submitted data
        $disciplinas = $this->model->getAllDisciplinas();
        $professores = $this->model->getAllProfessores();
        $professorsLookup = [];
        foreach ($professores as $professor) {
            $professorsLookup[$professor['id_professor']] = $professor['nome'];
        }
        $nomeDisciplinaAtual = htmlspecialchars($conteudoData['nomeDisciplina'] ?? ''); // For update display
        
        require_once __DIR__ . '/../views/conteudo/Create_edit.php';
    }

    // Handles listing all contents
    public function listContents() {
        $conteudos = $this->model->getAllConteudos();
        require_once __DIR__ . '/../views/conteudo/List.php';
    }

    // Handles deleting a content
    public function deleteContent($id_conteudo) {
        if ($this->model->deleteConteudo($id_conteudo)) {
            $_SESSION['message'] = "Conteúdo excluído com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao excluir conteúdo.";
        }
        header("Location: /conteudo?action=list");
        exit();
    }

    // Private method for validation
    private function validate($data) {
        $errors = [];

        if (
            empty($data["codigoConteudo"]) ||
            empty($data["tituloConteudo"]) ||
            empty($data["descricaoConteudo"]) ||
            empty($data["data_postagem"]) ||
            empty($data["professor"]) ||
            empty($data["disciplina"]) ||
            empty($data["tipo_conteudo"]) ||
            empty($data["id_disciplina"])
        ) {
            $errors[] = "Todos os campos devem ser preenchidos.";
        }

        if (strlen($data["codigoConteudo"]) < 5 || strlen($data["codigoConteudo"]) > 20) {
            $errors[] = "Erro: campo 'Código do Conteúdo' deve ter entre 5 e 20 caracteres.";
        }

        if (strlen($data["tituloConteudo"]) < 5 || strlen($data["tituloConteudo"]) > 40) {
            $errors[] = "Erro: campo 'Titulo de Conteúdo' deve ter entre 5 e 40 caracteres.";
        }

        if (strlen($data["descricaoConteudo"]) < 30 || strlen($data["descricaoConteudo"]) > 300) {
            $errors[] = "Erro: campo 'Descrição do Conteúdo' deve ter entre 30 e 300 caracteres.";
        }

        if (strlen($data["professor"]) < 5 || strlen($data["professor"]) > 20) {
            $errors[] = "Erro: campo 'Professor' deve ter entre 5 e 20 caracteres.";
        }

        if (strlen($data["disciplina"]) < 5 || strlen($data["disciplina"]) > 20) {
            $errors[] = "Erro: campo 'Disciplina' deve ter entre 5 e 20 caracteres.";
        }

        if (strlen($data["tipo_conteudo"]) < 5 || strlen($data["tipo_conteudo"]) > 20) {
            $errors[] = "Erro: campo 'Tipo de Conteúdo' deve ter entre 5 e 20 caracteres.";
        }

        return $errors;
    }
}

// --- Controller Instantiation and Routing ---
// This part acts as a basic router for the Conteudo module.
// In a full MVC framework, this would be handled by a more sophisticated central router.

$controller = new Conteudo_Controller($conexao);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->processForm();
} elseif (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'create':
            $controller->showForm();
            break;
        case 'edit':
            if (isset($_GET['id'])) {
                $controller->showForm($_GET['id']);
            } else {
                $_SESSION['error'] = "ID do conteúdo não especificado para edição.";
                header("Location: /conteudo?action=list");
            }
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                $controller->deleteContent($_GET['id']);
            } else {
                $_SESSION['error'] = "ID do conteúdo não especificado para exclusão.";
                header("Location: /conteudo?action=list");
            }
            break;
        case 'list':
            $controller->listContents();
            break;
        default:
            header("Location: /conteudo?action=list"); // Default to list if action is invalid
            break;
    }
} else {
    // If no action is specified, default to listing
    header("Location: /conteudo?action=list");
}