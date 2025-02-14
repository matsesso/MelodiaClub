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
        
        <!-- Área de seleção de músicas -->
        <div class="music-selector">
            <label for="musicSelect">Escolha uma música:</label>
            <select id="musicSelect">
                <?php include 'opcoes_musicas.php'; ?>
            </select>
        </div>

        <!-- Área da partitura -->
        <div id="paper"></div>
        
        <!-- Controles de áudio -->
        <div id="audio-controls"></div>

        <!-- Input de texto -->
         <form action="" method="post">
            <label for="nota">Coloque uma nota:</label>
            <input type="text" name="nota" id="nota">
            <button type="submit">Adicionar</button>
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
    </div>

    <?php
    if(isset($_POST['nota'])){
        $nota = $_POST['nota'];
        
        // Abre o arquivo para adicionar a nota
        $arquivo = fopen("notas.txt", "a");
        fwrite($arquivo, $nota . "\n");
        fclose($arquivo);
        
        // Redireciona para evitar reenvio do formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-basic.min.js"></script>
    <script src="musicas.php"></script>
    <script src="main.php"></script>
</body>
</html>
