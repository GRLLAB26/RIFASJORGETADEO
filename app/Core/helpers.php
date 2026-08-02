<?php

function view(string $view, array $data = [])
{
    extract($data);

    $viewFile = __DIR__ . '/../Views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        die("Vista no encontrada: {$view}");
    }

    ob_start();

    require $viewFile;

    $content = ob_get_clean();

    $title = $title ?? 'GRL RIFAS';

    $layout = __DIR__ . '/../Views/Layouts/Main.php';

    if (file_exists($layout)) {
        require $layout;
    } else {
        echo $content;
    }
}