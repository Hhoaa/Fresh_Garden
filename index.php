<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$controller = ucfirst($controller) . 'Controller';
$controllerFile = 'Controllers/' . $controller . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerInstance = new $controller();
    if (method_exists($controllerInstance, $action)) {
        $result = $controllerInstance->$action();
        if (is_array($result) && isset($result['view'])) {
            if (isset($result['data'])) {
                extract($result['data']);
            }
            require_once 'Views/' . $result['view'];
        }
    } else {
        die('Hành động không tồn tại!');
    }
} else {
    die('Controller không tồn tại!');
}
?>