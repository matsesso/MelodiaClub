<?php
$musicas = [
    'garotaIpanema' => 'Garota de Ipanema',
    'asabranca' => 'Asa Branca',
    'belaeafera' => 'Bela e a Fera'
];

foreach ($musicas as $valor => $nome) {
    echo "<option value=\"$valor\">$nome</option>\n";
}
?> 