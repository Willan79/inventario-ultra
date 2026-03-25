<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - Inventario Ultra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
        }
        .error-icon {
            font-size: 80px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #adb5bd;
            line-height: 1;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 28px;
            color: #343a40;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-home:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="bi bi-question-circle error-icon"></i>
        <div class="error-code">404</div>
        <h1 class="error-title">Página No Encontrada</h1>
        <p class="error-message">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        
        <a href="{{ route('web.dashboard') }}" class="btn btn-home">
            <i class="bi bi-house"></i> Volver al Dashboard
        </a>
    </div>
</body>
</html>
