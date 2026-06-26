<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swagger M57</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; background: #f4f1ea; }
        .top {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd4c8;
            background: #fffdf9;
            font-family: Georgia, serif;
        }
        .top a { color: #111; text-decoration: none; }
    </style>
</head>
<body>
    <div class="top">
        <a href="{{ route('docs.index') }}">Volver a docs</a>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.ui = SwaggerUIBundle({
            url: '{{ route('docs.openapi') }}',
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [SwaggerUIBundle.presets.apis],
        });
    </script>
</body>
</html>
