<?php
// Inicia a sessão
session_start();

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

// Limpa o arquivo quando o botão de limpar é pressionado
if(isset($_POST['limpar'])) {
    file_put_contents("Txts/notas.txt", ""); // Apenas limpa as notas
    file_put_contents("Txts/tom.txt", "C"); // Reseta a tonalidade para C
    $_SESSION['quantidadeDeCompassos'] = 0;
    $_SESSION['quebraDeLinha'] = 0;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

//Retira a última nota da partitura
if(isset($_POST['retirar'])) {
    $conteudo = file_get_contents("Txts/notas.txt");
    
    $contador = 0;
    $novo_conteudo = preg_replace_callback('/[^ |\n]|\|/', function ($match) use (&$contador) {
        return (++$contador > 2) ? $match[0] : ''; // Mantém apenas os caracteres após remover 2 reais
    }, strrev($conteudo));

    // Reverte de volta à ordem original
    $novo_conteudo = strrev($novo_conteudo);

    // Sobrescreve o arquivo com o novo conteúdo
    file_put_contents("Txts/notas.txt", $novo_conteudo);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Processa o novo formulário de compasso
if(isset($_POST['compasso'])){
    $compasso = $_POST['compasso'];
    if(filesize("Txts/compasso.txt") > 0){
        file_put_contents("Txts/compasso.txt", "");
    }
    file_put_contents("Txts/compasso.txt", $compasso);
    
    // Extrai o numerador do compasso para definir o total de tempos
    list($numerador, $denominador) = explode('/', $compasso);
    $_SESSION['totalTemposCompasso'] = (float)$numerador;
    $_SESSION['quantidadeDeCompassos'] = 0; // Reseta o contador
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

//Adiciona acorde ao compasso
if(isset($_POST['acorde']) && isset($_POST['numero_compasso'])) {
    $acorde = $_POST['acorde'];
    $numero_compasso = (int)$_POST['numero_compasso'];
    
    // Lê o conteúdo atual do arquivo
    $conteudo = file_get_contents("Txts/notas.txt");
    
    // Divide o conteúdo em compassos usando o delimitador |
    $compassos = explode("|", $conteudo);
    
    // Verifica se o número do compasso é válido
    if($numero_compasso > 0 && $numero_compasso <= count($compassos)) {
        // Índice do array é número do compasso - 1
        $indice = $numero_compasso - 1;
        
        // Remove qualquer acorde existente (texto entre aspas duplas)
        $compassos[$indice] = preg_replace('/"[^"]*"/', '', $compassos[$indice]);
        
        // Adiciona o novo acorde no início do compasso
        $compassos[$indice] = ' "' . $acorde . '" ' . trim($compassos[$indice]);
        
        // Junta os compassos novamente
        $novo_conteudo = implode("|", $compassos);
        
        // Salva o conteúdo atualizado
        file_put_contents("Txts/notas.txt", $novo_conteudo);
        $_SESSION['sucesso'] = "Acorde adicionado ao compasso $numero_compasso!";
    } else {
        $_SESSION['erro'] = "Número de compasso inválido!";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Processa o novo formulário de tonalidade
if(isset($_POST['tom'])){
    $tom = $_POST['tom'];
    if(filesize("Txts/tom.txt") > 0){
        file_put_contents("Txts/tom.txt", "");
    }
    file_put_contents("Txts/tom.txt", $tom);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


if(isset($_POST['nota'])){
    $nota = $_POST['nota'];
    
    // Verifica se a nota não está vazia
    if (!empty($nota)) {
        $tempo = $_POST['nota_select'];

        if(!empty($nota)) {
            $oitava = $_POST['oitava'];
            $nota = $nota . $oitava;
        }
        
        // Converte a fração em número decimal
        if (strpos($tempo, '/') !== false) {
            list($numerador, $denominador) = explode('/', $tempo);
            $valor_tempo = $numerador / $denominador;
        } else {
            $valor_tempo = (float)$tempo;
        }
        
        // Lê o compasso atual
        $compasso = file_get_contents("Txts/compasso.txt");
        list($numerador, $denominador) = explode('/', $compasso);
        $totalTemposCompasso = (float)$numerador;
        
        // Calcula o espaço disponível no compasso atual
        $espacoDisponivel = $totalTemposCompasso - $_SESSION['quantidadeDeCompassos'];
        
        // Abre o arquivo para adicionar a nota
        $arquivo = fopen("Txts/notas.txt", "a");
        if ($arquivo) {
            // Se a nota é maior que o espaço disponível
            if ($valor_tempo > $espacoDisponivel) {
                $tempo_restante = $valor_tempo;
                $precisa_ligar = true; // Flag para indicar que precisamos de ligaduras
                
                // Primeira parte - completa o compasso atual
                if ($espacoDisponivel > 0) {
                    fwrite($arquivo, $nota . $espacoDisponivel . "-");
                    $tempo_restante -= $espacoDisponivel;
                    fwrite($arquivo, " | ");
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        fwrite($arquivo, "\\n");
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
                
                // Compassos completos intermediários
                while ($tempo_restante > $totalTemposCompasso) {
                    fwrite($arquivo, "-" . $nota . $totalTemposCompasso . "-");
                    $tempo_restante -= $totalTemposCompasso;
                    fwrite($arquivo, " | ");
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        fwrite($arquivo, "\\n");
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
                
                // Última parte - resto que sobrou
                if ($tempo_restante > 0) {
                    fwrite($arquivo, "-" . $nota . $tempo_restante);
                    $_SESSION['quantidadeDeCompassos'] = $tempo_restante;
                } else {
                    $_SESSION['quantidadeDeCompassos'] = 0;
                }
                
            } else {
                // Nota cabe no compasso atual
                fwrite($arquivo, $nota . $tempo);
                $_SESSION['quantidadeDeCompassos'] += $valor_tempo;
                
                // Se completou exatamente o compasso
                if(abs($_SESSION['quantidadeDeCompassos'] - $totalTemposCompasso) < 0.0001){
                    fwrite($arquivo, " | ");
                    $_SESSION['quantidadeDeCompassos'] = 0;
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        fwrite($arquivo, "\\n");
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
            }
            
            fclose($arquivo);
            $_SESSION['sucesso'] = "Nota adicionada com sucesso!";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Salva a música com um nome
if(isset($_POST['salvar_musica']) && isset($_POST['nome_musica'])) {
    $nome_musica = preg_replace("/[^a-zA-Z0-9]/", "_", $_POST['nome_musica']); // Remove caracteres especiais
    
    $dados_musica = [
        'notas' => file_get_contents("Txts/notas.txt"),
        'tom' => file_get_contents("Txts/tom.txt"),
        'compasso' => file_get_contents("Txts/compasso.txt")
    ];

    //Crie um arquivo txt com o nome da música
    $arquivo_musica = "Musicas/" . $nome_musica . ".txt";
    
    if(file_put_contents($arquivo_musica, json_encode($dados_musica))) {
        $_SESSION['sucesso'] = "Música '$nome_musica' salva com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao salvar a música!";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Carrega uma música salva
if(isset($_POST['carregar_musica'])) {
    $nome_musica = $_POST['carregar_musica'];
    $arquivo_musica = "Musicas/" . $nome_musica . ".txt";
    
    if(file_exists($arquivo_musica)) {
        $dados_musica = json_decode(file_get_contents($arquivo_musica), true);
        
        if($dados_musica) {
            // Restaura os dados da música
            file_put_contents("Txts/notas.txt", $dados_musica['notas']);
            file_put_contents("Txts/tom.txt", $dados_musica['tom']);
            file_put_contents("Txts/compasso.txt", $dados_musica['compasso']);
            
            // Reseta os contadores
            $_SESSION['quantidadeDeCompassos'] = 0;
            $_SESSION['quebraDeLinha'] = 0;
            
            $_SESSION['sucesso'] = "Música '$nome_musica' carregada com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao carregar a música!";
        }
    } else {
        $_SESSION['erro'] = "Arquivo da música não encontrado!";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
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
        <div class="controls">
            <label for="tempo">Bpm:</label>
            <input type="text" id="bpm" name="bpm">
            <button id="play">▶ Tocar Música</button>
            <button id="stop">⏹ Parar</button>
        </div>

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
