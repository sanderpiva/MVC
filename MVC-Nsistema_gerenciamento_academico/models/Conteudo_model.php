<?php

// Assume conexao.php is now at the root of the project, or in 'config/'
// Adjust path as needed, e.g., require_once __DIR__ . '/../../config/conexao.php';
//require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/conexao.php';

class Conteudo_Model {
    private $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    public function getAllDisciplinas() {
        $stmt = $this->conexao->query("SELECT * FROM disciplina");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProfessores() {
        $stmt = $this->conexao->query("SELECT * FROM professor");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConteudoById($id_conteudo) {
        $stmt = $this->conexao->prepare("SELECT c.*, d.nome AS nomeDisciplina, d.Professor_id_professor
                                         FROM conteudo c
                                         JOIN disciplina d ON c.Disciplina_id_disciplina = d.id_disciplina
                                         WHERE c.id_conteudo = :id");
        $stmt->execute([':id' => $id_conteudo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createConteudo($data) {
        $sql = "INSERT INTO conteudo (codigoConteudo, titulo, descricao, data_postagem, professor, disciplina, tipo_conteudo, Disciplina_id_disciplina)
                VALUES (:codigo, :titulo, :descricao, :data_postagem, :professor, :disciplina, :tipo, :id_disciplina)";
        $stmt = $this->conexao->prepare($sql);
        return $stmt->execute([
            ':codigo' => $data['codigoConteudo'],
            ':titulo' => $data['tituloConteudo'],
            ':descricao' => $data['descricaoConteudo'],
            ':data_postagem' => $data['data_postagem'],
            ':professor' => $data['professor'],
            ':disciplina' => $data['disciplina'],
            ':tipo' => $data['tipo_conteudo'],
            ':id_disciplina' => $data['id_disciplina']
        ]);
    }

    public function updateConteudo($data) {
        $sql = "UPDATE conteudo SET
                    codigoConteudo = :codigo,
                    titulo = :titulo,
                    descricao = :descricao,
                    data_postagem = :data_postagem,
                    professor = :professor,
                    disciplina = :disciplina,
                    tipo_conteudo = :tipo
                WHERE id_conteudo = :id_conteudo";
        $stmt = $this->conexao->prepare($sql);
        return $stmt->execute([
            ':codigo' => $data['codigoConteudo'],
            ':titulo' => $data['tituloConteudo'],
            ':descricao' => $data['descricaoConteudo'],
            ':data_postagem' => $data['data_postagem'],
            ':professor' => $data['professor'],
            ':disciplina' => $data['disciplina'],
            ':tipo' => $data['tipo_conteudo'],
            ':id_conteudo' => $data['id_conteudo']
        ]);
    }

    public function deleteConteudo($id_conteudo) {
        $stmt = $this->conexao->prepare("DELETE FROM conteudo WHERE id_conteudo = :id");
        return $stmt->execute([':id' => $id_conteudo]);
    }

    public function getAllConteudos() {
        $stmt = $this->conexao->query("SELECT c.*, d.nome AS nomeDisciplina, d.Professor_id_professor
                                         FROM conteudo c
                                         JOIN disciplina d ON c.Disciplina_id_disciplina = d.id_disciplina");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}