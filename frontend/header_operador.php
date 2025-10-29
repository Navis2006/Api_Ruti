<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Operador</title>
    <!-- Carga de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Carga de Fuente LATO -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <div>
                <a href="menu_trailero.php" class="text-2xl font-bold text-blue-600">Dunosusa</a>
            </div>
            <!-- Menú de Navegación -->
            <div class="flex flex-wrap items-center space-x-4 mt-4 md:mt-0">
                <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                <a href="menu_trailero.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'menu_trailero.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Inicio</a>
                <a href="rutas_asignadas.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'rutas_asignadas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mis Viajes</a>
                <a href="trailer_asignado.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'trailer_asignado.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Vehículo</a>
                <a href="alertas.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'alertas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Alertas</a>
                <a href="perfil.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'perfil.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Perfil</a>
                <a href="contacto_soporte.php" class="px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'contacto_soporte.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Soporte</a>
                <a href="../backend/logout.php" class="bg-red-500 text-white px-4 py-2 -my-2 rounded-md hover:bg-red-600">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="container mx-auto p-6 md:p-10">
        <!-- El contenido específico de cada página PHP irá aquí -->
