<?php

// debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// composer autoload
require_once '../vendor/autoload.php';

use MelodiaClub\controllers\MusicSheetController; 

$controller = new MusicSheetController();
$abcNotation = $controller->getAbcNotation();
$availableMusics = $controller->getAvailableMusics();

// Processa as requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    MusicSheetController::validation($_POST);
}

// Renderiza a visualização
require_once '../views/MusicSheetView.php';
