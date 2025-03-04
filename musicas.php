<?php
header('Content-Type: application/javascript');

// Array para armazenar as notas
$notas = [];
$tom = "";

// Lê as notas do arquivo se ele existir
if (file_exists("Txts/notas.txt")) {
    $notas = file("Txts/notas.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

//Lê a tonalidade da partitura
if (file_exists("Txts/tom.txt") && filesize("Txts/tom.txt") > 0) {
    $tom = file_get_contents("Txts/tom.txt");
} else {
    $tom = "C";
    file_put_contents("Txts/tom.txt", "C"); // Cria o arquivo com tom C se não existir
}

// Lê o compasso da partitura
if (file_exists("Txts/compasso.txt") && filesize("Txts/compasso.txt") > 0) {
    $compasso = file_get_contents("Txts/compasso.txt");
} else {
    $compasso = "4/4";
}

if (file_exists("Txts/tempo.txt") && filesize("Txts/tempo.txt") > 0) {
    $tempoBpm = file_get_contents("Txts/tempo.txt");
} else {
    $tempoBpm = "60";
}

// Converte as notas em uma string ABC
$abc_notas = implode(" ", $notas);

// Cria a notação ABC completa
echo "const musicaABC = `
X:1
T:Suas Notas
M:$compasso
L:1/4
Q:$tempoBpm
K:$tom
|${abc_notas}|
`;";
?>