<?php
require_once("functions/geral.php");

function incluirClasses($nomeClasse)
{
    if (file_exists($nomeClasse . ".php")) {
        require_once($nomeClasse . ".php");
    }
}

function procurarDiretorio($nomeClasses)
{
    $array_paths = array(

        // Caso for chamado dentro de backend, classes ou outro dentro de classes:
        '',
        '' . DIRECTORY_SEPARATOR,
        '..' . DIRECTORY_SEPARATOR,
        'config' . DIRECTORY_SEPARATOR,
        'model' . DIRECTORY_SEPARATOR,
        'controller' . DIRECTORY_SEPARATOR,
        'view' . DIRECTORY_SEPARATOR,
        //'..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR,
        '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
        '..' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR,
        '..' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR,
        '..' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR,

        // Caso for chamado desde o dominio:
        'backend' . DIRECTORY_SEPARATOR,
        'backend' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
        'backend' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR,
        'backend' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR,
        'backend' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR,
    );

    foreach ($array_paths as $dir) {
        $arquivo = $dir . $nomeClasses . ".php";
        if (file_exists($arquivo)) {
            require_once($arquivo);
        }
    }
}

spl_autoload_register("procurarDiretorio");
