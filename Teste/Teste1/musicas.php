<?php
header('Content-Type: application/javascript');

// Array para armazenar as notas
$notas = [];
$tom = "";

// Lê as notas do arquivo se ele existir
if (file_exists("notas.txt")) {
    $notas = file("notas.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

//Lê a tonalidade da partitura
if (file_exists("tom.txt") && filesize("tom.txt") > 0) {
    $tom = file_get_contents("tom.txt");
} else {
    $tom = "C";
    file_put_contents("tom.txt", "C"); // Cria o arquivo com tom C se não existir
}

// Lê o compasso da partitura
if (file_exists("compasso.txt") && filesize("compasso.txt") > 0) {
    $compasso = file_get_contents("compasso.txt");
} else {
    $compasso = "4/4";
}

// Converte as notas em uma string ABC
$abc_notas = implode(" ", $notas);

// Cria a notação ABC completa
echo "const musicaABC = `
X:1
T:Suas Notas
M:$compasso
L:1/4
K:$tom
|${abc_notas}|
`;";
?>

const MUSICAS = {
    garotaIpanema: `
X:1
T:Garota de Ipanema
C:Tom Jobim
M:4/4
L:1/4
K:C
Q:120
V:1 clef=treble
w: O-lha que coi-sa mais lin-da, mais chei-a de gra-ça
"Am7"A2 ^G2 |"Gmaj7"G2 ^F2 |"Fmaj7"F2 E2 |"F#m7b5"E2 D2 |
w: É e-la me-ni-na que vem e que pas-sa
"Dm7"D2 ^C2 |"G7"C2 B,2 |"Cmaj7"C4- |"Cmaj7"C4 |
    `,

    asabranca: `
X:1
T:Asa Branca
C:Luiz Gonzaga
M:2/4
L:1/8
K:C
w: Quan-do o-lhei a ter-ra ar-den-do
"C"c2 e2 |"F"f2 g2 |"C"e2 c2 |"G7"d4 |
w: Qual fo-guei-ra de São João
"C"c2 e2 |"F"f2 g2 |"C"e2 c2 |"G7"d4 |
    `,

    belaeafera: `
X:1
T:Bela e a Fera
C:Alan Menken
Q:120
V:1 clef=treble
M:4/4
L:1/4
K:F
A,/2C/2E/2F/2 | B,2 z2 | A,/2C/2E/2F/2 | G2 z2
    `
}; 