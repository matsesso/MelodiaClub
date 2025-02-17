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
         <form action="" method="post">
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
                <option value="^B">B#</option>
                <option value="z">Pausa</option>
            </select>
            <select name="nota_select" id="nota_select">
                <option value="4">4</option>
                <option value="2">2</option>
                <option value="1">1</option>
                <option value="1/2">1/2</option>
                <option value="1/4">1/4</option>
                <option value="1/8">1/8</option>
                <option value="1/16">1/16</option>
                <option value="1/32">1/32</option>
            </select>
            <button type="submit">Adicionar</button>
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
         
        <!-- Adicionat acorde no compasso -->
         <form action="" method="post">
            <label for="acorde">Adicionar acorde ao compasso:</label>
            <input type="text" name="acorde" id="acorde">
            <button type="submit">Adicionar</button>
         </form>
         
         <!-- Botão para limpar notas -->
         <form action="" method="post">
            <input type="hidden" name="limpar" value="1">
            <button type="submit" class="limpar-notas">Limpar Notas</button>
         </form>

        <!-- Explicações -->
        <div class="explanation">
            <h2>Como funciona:</h2>
            <ul>
                <li>A partitura acima usa a notação ABC</li>
                <li>Os acordes são mostrados acima das notas</li>
                <li>A letra da música está sincronizada com as notas</li>
                <li>Use os controles de áudio para tocar a música</li>
            </ul>
        </div>
        <button id="play">▶ Tocar Música</button>
        <button id="stop">⏹ Parar</button>

    </div>

    <?php
    // Inicia a sessão
    session_start();
    
    // Garante que os arquivos existam
    if (!file_exists("notas.txt") || filesize("notas.txt") == 0) {
        file_put_contents("notas.txt", "");  // Arquivo vazio, sem estrutura básica
    }
    if (!file_exists("tom.txt")) {
        file_put_contents("tom.txt", "C");
    }
    
    // Inicializa o compasso e a quantidade de compassos na linha se não existir
    if (!isset($_SESSION['compasso'])) {
        $_SESSION['compasso'] = 0;
    }
    if (!isset($_SESSION['quebraDeLinha'])) {
        $_SESSION['quebraDeLinha'] = 0;
    }
    
    // Limpa o arquivo quando o botão de limpar é pressionado
    if(isset($_POST['limpar'])) {
        file_put_contents("notas.txt", ""); // Apenas limpa as notas
        file_put_contents("tom.txt", "C"); // Reseta a tonalidade para C
        $_SESSION['compasso'] = 0;
        $_SESSION['quebraDeLinha'] = 0;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    //Adiciona acorde ao compasso
    if(isset($_POST['acorde'])){
        $acorde = $_POST['acorde'];
        $arquivo = fopen("notas.txt", "a");
        fwrite($arquivo, '"' . $acorde . '"');
        fclose($arquivo);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Processa o novo formulário de tonalidade
    if(isset($_POST['tom'])){
        $tom = $_POST['tom'];
        if(filesize("tom.txt") > 0){
            file_put_contents("tom.txt", "");
        }
        file_put_contents("tom.txt", $tom);
    }
    
    if(isset($_POST['nota'])){
        $nota = $_POST['nota'];
        
        // Verifica se a nota não está vazia
        if (!empty($nota)) {
            $tempo = $_POST['nota_select'];
            
            // Converte a fração em número decimal
            if (strpos($tempo, '/') !== false) {
                list($numerador, $denominador) = explode('/', $tempo);
                $valor_tempo = $numerador / $denominador;
            } else {
                $valor_tempo = (float)$tempo;
            }
            
            $_SESSION['compasso'] += $valor_tempo;
            $nota = $nota . "" . $tempo;
            $_SESSION['quebraDeLinha']++;
            
            // Abre o arquivo para adicionar a nota
            $arquivo = fopen("notas.txt", "a");
            if ($arquivo) {
                fwrite($arquivo, $nota);
                
                // Adiciona barra de compasso quando completar exatamente 4 tempos
                if(abs($_SESSION['compasso'] - 4) < 0.0001){ // usando uma pequena margem de erro para comparação de ponto flutuante
                    fwrite($arquivo, " | ");
                    $_SESSION['compasso'] = 0;
                }

                //Adiciona quebra de linha quando completar 5 tempos
                if($_SESSION['quebraDeLinha'] == 5){
                    fwrite($arquivo, "|" . "\\n" . "|");
                    $_SESSION['quebraDeLinha'] = 0;
                }
                
                fclose($arquivo);
                
                // Redireciona para evitar reenvio do formulário
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
    ?>

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

                try {
                    await synthControl.init({
                        visualObj: visualObj,
                        audioContext: audioContext,
                        millisecondsPerMeasure: 2000
                    });

                    await synthControl.prime(); // Prepara o áudio para evitar atrasos
                    synthControl.start(); // Toca a música
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
</body>
</html>
