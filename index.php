<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Processa as requisições POST diretamente no controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    MusicSheetController::validation($_POST);
}

// Renderiza a visualização
require_once 'views/MusicSheetView.php';
