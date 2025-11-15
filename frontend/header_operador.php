<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Operador</title>

    <!-- Frameworks y Estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js para interactividad -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Estilos personalizados -->
    <style>
        body { 
            font-family: 'Lato', sans-serif; 
        }
        
        /* Oculta elementos con x-cloak hasta que Alpine.js se inicialice */
        [x-cloak] { 
            display: none !important; 
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Encabezado principal -->
    <header class="bg-white shadow-md sticky top-0 z-30">
        <!-- Contenedor del menú de navegación -->
        <!-- x-data inicializa el estado de Alpine.js para el menú móvil -->
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center" x-data="{ mobileMenuOpen: false }">
            
            <!-- Logo o Marca -->
            <div>
                <a href="menu_trailero.php" class="text-2xl font-bold text-blue-600">Dunosusa</a>
            </div>

            <!-- Botón del menú móvil (hamburguesa) -->
            <div class="md:hidden">
                <!-- @click alterna el valor de mobileMenuOpen -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Contenedor de los enlaces de navegación -->
            <!-- 
                Clases condicionales con :class:
                - 'block': Muestra el menú si mobileMenuOpen es true.
                - 'hidden': Oculta el menú si mobileMenuOpen es false.
                
                Clases responsivas de Tailwind CSS:
                - Inicia oculto ('hidden') en pantallas pequeñas.
                - Se muestra como flex ('md:flex') en pantallas medianas y grandes.
            -->
            <div 
                :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
                class="hidden md:flex flex-col md:flex-row md:items-center w-full md:w-auto absolute md:static left-0 top-full bg-white md:bg-transparent shadow-md md:shadow-none p-4 md:p-0 space-y-2 md:space-y-0 md:space-x-4"
                x-cloak
            >
                <?php 
                    // Obtiene el nombre del archivo PHP actual para resaltar el enlace activo.
                    $currentPage = basename($_SERVER['PHP_SELF']); 
                ?>

                <!-- Enlaces de Navegación -->
                <a href="menu_trailero.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'menu_trailero.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Inicio</a>
                <a href="rutas_asignadas.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'rutas_asignadas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mis Viajes</a>
                <a href="trailer_asignado.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'trailer_asignado.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Vehículo</a>
                <a href="alertas.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'alertas.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Alertas</a>
                <a href="perfil.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'perfil.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Mi Perfil</a>
                <a href="contacto_soporte.php" class="block px-2 py-1 rounded-md text-gray-600 hover:text-blue-600 <?= ($currentPage == 'contacto_soporte.php') ? 'bg-blue-100 text-blue-700 font-semibold' : '' ?>">Soporte</a>
                
                <!-- Botón para cerrar sesión -->
                <a href="../backend/logout.php" class="block mt-2 md:mt-0 md:inline-block bg-red-500 text-white text-center px-4 py-2 rounded-md hover:bg-red-600">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <!-- Contenido principal de la página -->
    <main class="container mx-auto p-6 md:p-10">
        <!-- El contenido específico de cada página iría aquí -->
    </main>

</body>
</html>