<!DOCTYPE html>
<html>
<head>
    <title>Dashboard do Professor</title>
    <meta charset="utf=8">
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        /* Nao consigo acessar: href="/public/css/style.css" */
        /* Your existing styles can go here or in style.css */
        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sections-wrapper {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        .section {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .section h2 {
            margin-top: 0;
            color: #333;
        }
        .button-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .button-grid button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .button-grid button:hover {
            background-color: #0056b3;
        }
        .home-link {
            text-align: center;
            margin-top: 30px;
        }
        .home-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .home-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="servicos_forms">
    <div class="container">
        <h1>Dashboard do Professor</h1>
        <hr>
        <div class="sections-wrapper">
            <section class="section">
                <h2>CADASTROS</h2>
                <div class="button-grid">
                    <button onclick="window.location.href='/turma?action=create'">Cadastrar Turma</button>
                    <button onclick="window.location.href='/disciplina?action=create'">Cadastrar Disciplina</button>
                    <button onclick="window.location.href='/matricula?action=create'">Cadastrar Matricula</button>
                    <button onclick="window.location.href='/conteudo?action=create'">Cadastrar Conteudo</button>
                    <button onclick="window.location.href='/prova?action=create'">Cadastrar Prova</button>
                    <button onclick="window.location.href='/questoes-prova?action=create'">Cadastrar Questoes de prova</button>
                    <button onclick="window.location.href='/respostas?action=create'">Cadastrar Respostas</button>
                </div>
            </section>

            <section class="section">
                <h2>CONSULTAS</h2>
                <div class="button-grid">
                    <button onclick="window.location.href='/turma?action=list'">Consultar Turma</button>
                    <button onclick="window.location.href='/disciplina?action=list'">Consultar Disciplina</button>
                    <button onclick="window.location.href='/matricula?action=list'">Consultar Matricula</button>
                    <button onclick="window.location.href='/conteudo?action=list'">Consultar Conteudo</button>
                    <button onclick="window.location.href='/prova?action=list'">Consultar Prova</button>
                    <button onclick="window.location.href='/questoes-prova?action=list'">Consultar Questoes de prova</button>
                    <button onclick="window.location.href='/respostas?action=list'">Consultar Respostas</button>
                    <button onclick="window.location.href='/aluno?action=list'">Consultar Aluno</button>
                    <button onclick="window.location.href='/professor?action=list'">Consultar Professor</button>
                </div>
            </section>
        </div>
        <div class="home-link">
            <a href="/index.php?logout=true">Logout -> HomePage</a>
        </div>
    </div><hr><hr>

    <footer class="servicos">
        <p>Desenvolvido por Juliana e Sander</p>
    </footer>
</body>
</html>