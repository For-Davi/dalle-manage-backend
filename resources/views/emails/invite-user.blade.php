<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail de convite para Dalle Manage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: auto;
        }

        h1 {
            color: #333333;
        }

        p {
            color: #555555;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888888;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Olá {{ $userName }}!</h1>
        <p>O usuário {{ $adminName }} da empresa {{ $enterpriseName }}, incluiu você na <strong>Dalle Manage</strong>. Para continuar com o processo de
            login, por favor, clique no link abaixo:</p>
            <p>
                Seus dados de acesso:
            </p>
            <p>
                - Email: {{ $userEmail }}
            </p>
            <p>
                - Senha: Senha definida por {{ $adminName }}
            </p>
        <p>
            <a class="button" href="{{ $appUrl }}/auth">Fazer Login</a>
        </p>
        <p>
            Ou você pode <strong>redefinir sua senha</strong> clicando no link abaixo:
        </p>
        <p>
            <a class="button" href="{{ $appUrl }}/reset-password/{{ urlencode($token) }}">Redefinir Senha</a>
        </p>

        <p>Atenciosamente,<br>A equipe do Dalle Manage.</p>
        <div class="footer">
            <p>© {{ date('Y') }} Dalle Manage. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>
