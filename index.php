<?php

require_once 'app/Controllers/MusicSheetController.php';

use App\Controllers\MusicSheetController;

$musicSheet = new MusicSheetController();

// Garante que os arquivos e diretórios existam
if (!file_exists("Musicas")) {
    mkdir("Musicas", 0777, true);
}
if (!file_exists("Txts/notas.txt") || filesize("Txts/notas.txt") == 0) {
    file_put_contents("Txts/notas.txt", "");  // Arquivo vazio, sem estrutura básica
}
if (!file_exists("Txts/tom.txt")) {
    file_put_contents("Txts/tom.txt", "C");
}

// Inicializa o compasso e a quantidade de compassos na linha se não existir
if (!isset($_SESSION['quantidadeDeCompassos'])) {
    $_SESSION['quantidadeDeCompassos'] = 0;
}
if (!isset($_SESSION['quebraDeLinha'])) {
    $_SESSION['quebraDeLinha'] = 0;
}

//Adicionar Ritornelo
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial ABCJS</title>
    <!-- Estilos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Tutorial de Partitura Musical</h1>
    

        <!-- Área da partitura -->
        <div id="paper"></div>
        
        <!-- Input de texto -->
         <form action="app/Controllers/MusicSheetController.php" method="post">
            <label for="nota">Coloque uma nota:</label>
            <select name="nota" id="nota">
                <option value="C">C</option>
                <option value="^C">C#</option>
                <option value="_C">Cb</option>
                <option value="D">D</option>
                <option value="^D">D#</option>
                <option value="_D">Db</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="^F">F#</option>
                <option value="_F">Fb</option>
                <option value="G">G</option>
                <option value="^G">G#</option>
                <option value="A">A</option>
                <option value="^A">A#</option>
                <option value="B">B</option>
                <option value="z">Pausa</option>
            </select>
            <select name="nota_select" id="nota_select">
                <option value="4">4</option>
                <option value="2">2</option>
                <option value="1" selected>1</option>
                <option value="1/2">1/2</option>
                <option value="1/4">1/4</option>
                <option value="1/8">1/8</option>
                <option value="1/16">1/16</option>
                <option value="1/32">1/32</option>
                <option value=".">stacato</option>
            </select>
            <select name="oitava" id="oitava">
                <option value="" select>oitava padrão</option>
                <option value="'">1 oitava acima</option>
                <option value="''">2 oitava acimas</option>
                <option value="'''">3 oitava acimas</option>
                <option value=",">1 oitava abaixo</option>
                <option value=",,">2 oitava abaixos</option>
                <option value=",,,">3 oitava abaixos</option>
            </select>
            <button type="submit">Adicionar</button>
         </form>

         <!-- Adicinar ritornelo -->
          <form action="" method="post">
            <input type="hidden" name="ritornelo" value=":">
            <button type="submit">Adicionar Ritornelo</button>
          </form>

         <!-- Botão para alterar a tonalidade -->
         <form action="" method="post">
            <label for="tom">Tonalidade:</label>
            <select name="tom" id="tom">
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="G">G</option>
                <option value="A">A</option>
                <option value="B">B</option>
            </select>
            <button type="submit">Alterar</button>
         </form>
         
        <!-- Adicionar acorde no compasso -->
         <form action="" method="post">
            <label for="acorde">Adicionar acorde ao compasso:</label>
            <input type="text" name="acorde" id="acorde">
            <label for="numero_compasso">Número do compasso:</label>
            <input type="number" name="numero_compasso" id="numero_compasso" min="1" required>
            <button type="submit">Adicionar</button>
         </form>

         <!-- Alterar compasso -->
         <form action="" method="post">
            <label for="compasso">Alterar compasso:</label>
            <select name="compasso" id="compasso">
                <option value="4/4">4/4</option>
                <option value="3/4">3/4</option>
                <option value="2/4">2/4</option>
                <option value="1/4">1/4</option>
                <option value="2/3">2/3</option>
                <option value="2/2">2/2</option>
                <option value="3/8">3/8</option>
            </select>
            <button type="submit">Alterar</button>
         </form>
         
         <!-- Botão para limpar notas -->
         <form action="" method="post">
            <input type="hidden" name="limpar" value="1">
            <button type="submit" class="limpar-notas">Limpar Notas</button>
         </form>

         <!-- Botão retirar última nota -->
          <form action="" method="post">
            <input type="hidden" name="retirar" value="1">
            <button type="submit" class="limpar-notas">Retirar nota</button>
          </form>

         <!-- Salvar Música -->
         <form action="" method="post">
            <label for="nome_musica">Nome da música:</label>
            <input type="text" name="nome_musica" id="nome_musica" required>
            <button type="submit" name="salvar_musica">Salvar Música</button>
         </form>

         <!-- Carregar Música -->
         <form action="" method="post">
            <label for="carregar_musica">Carregar música:</label>
            <select name="carregar_musica" id="carregar_musica">
                <?php
                $musicas = glob("Musicas/*.txt");
                foreach($musicas as $musica) {
                    $nome = basename($musica, '.txt');
                    echo "<option value='$nome'>$nome</option>";
                }
                ?>
            </select>
            <button type="submit">Carregar</button>
         </form>

        <!-- Adicione este controle de velocidade antes dos botões de play/stop -->
         <form action="" method="post">
            <div class="controls">
                <label for="tempo">Bpm:</label>
                <input type="text" id="bpm" name="bpm">
                <button id="play" type="submit">▶ Tocar Música</button>
                <button id="stop">⏹ Parar</button>
            </div>
         </form>

    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-basic.min.js"></script>
    <script src="musicas.php"></script>
    <script src="main.php"></script>
    <script>

        document.addEventListener("DOMContentLoaded", async function () {
            // Renderiza a partitura na tela
            let visualObj = ABCJS.renderAbc("paper", musicaABC, { responsive: "resize" })[0];

            // Verifica se o navegador suporta áudio
            if (!ABCJS.synth.supportsAudio()) {
                alert("Seu navegador não suporta áudio no ABCJS!");
                return;
            }

            let audioContext = new (window.AudioContext || window.webkitAudioContext)();
            let synthControl = new ABCJS.synth.CreateSynth();

            async function playMusic() {
                if (synthControl) synthControl.stop();

                // Pega a velocidade selecionada
                let bpm = parseInt(document.getElementById('bpm').value);
                bpm = (60 / bpm) * 1000;

                try {
                    await synthControl.init({
                        visualObj: visualObj,
                        audioContext: audioContext,
                        millisecondsPerMeasure: bpm // Usa o valor selecionado
                    });

                    await synthControl.prime();
                    synthControl.start();
                } catch (error) {
                    console.error("Erro ao tocar a música:", error);
                }
            }

            function stopMusic() {
                if (synthControl) {
                    synthControl.stop();
                }
            }

            document.getElementById("play").addEventListener("click", playMusic);
            document.getElementById("stop").addEventListener("click", stopMusic);
        });
    </script>

    <!-- Adicione isso logo após a abertura da tag body ou onde desejar mostrar a mensagem de erro -->
    <?php if (isset($_SESSION['erro'])) {
        echo '<div class="erro">' . $_SESSION['erro'] . '</div>';
        unset($_SESSION['erro']);
    }
    if (isset($_SESSION['sucesso'])) {
        echo '<div class="sucesso">' . $_SESSION['sucesso'] . '</div>';
        unset($_SESSION['sucesso']);
    } ?>
</body>
</html>
