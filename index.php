<?php
/**
 * SecuLab CTF - Routeur Principal
 * Application volontairement vulnérable pour l'apprentissage de la cybersécurité
 */

session_start();

// Chargement de la configuration
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

// Récupération de la route
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// Routage simple
$routes = [
    '/'         => 'pages/home.php',
    '/login'    => 'modules/auth.php',
    '/logout'   => 'modules/logout.php',
    '/profile'  => 'modules/profile.php',
    '/wall'     => 'modules/wall.php',
    '/calc'     => 'modules/calc.php',
    '/admin'    => 'modules/admin.php',
    '/debug'    => 'modules/debug.php',
    '/secubot'  => 'modules/secubot.php',
    '/sql'      => 'modules/sqlquery.php',
];

// Dispatch vers le bon module (AVANT le header pour permettre les redirections)
if (isset($routes[$path])) {
    $file = __DIR__ . '/' . $routes[$path];
    if (file_exists($file)) {
        // Capturer la sortie du module pour l'afficher après le header
        ob_start();
        include $file;
        $content = ob_get_clean();
    } else {
        $content = '<div class="container"><h1>Module en construction...</h1></div>';
    }
} else {
    http_response_code(404);
    $content = '<div class="container"><h1>404 - Page non trouvée</h1></div>';
}

// Inclusion du header APRÈS l'exécution du module
include __DIR__ . '/includes/header.php';

// Afficher le contenu du module
echo $content;

// Inclusion du footer
include __DIR__ . '/includes/footer.php';
