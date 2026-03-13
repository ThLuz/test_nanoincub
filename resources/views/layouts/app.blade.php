<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Funcionários</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white shadow-lg mb-6">
        <div class="container mx-auto font-bold">Painel - Admin</div>
    </nav>

    <main class="container mx-auto">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>