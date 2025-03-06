<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Partituras Musicais</title>
    <!-- Estilos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <!-- Mensagens de erro/sucesso -->
    <?php if (isset($_SESSION['erro'])) {
        echo '<div class="mensagem erro"><span>⚠️</span>' . $_SESSION['erro'] . '<button class="fechar-mensagem">×</button></div>';
        unset($_SESSION['erro']);
    }
    if (isset($_SESSION['sucesso'])) {
        echo '<div class="mensagem sucesso"><span>✓</span>' . $_SESSION['sucesso'] . '<button class="fechar-mensagem">×</button></div>';
        unset($_SESSION['sucesso']);
    } ?>

    <div class="container">
        <header>
            <h1 class="title">MelodiaClub - Editor de Partituras Musicais</h1>
        </header>

        <!-- Área da partitura -->
        <div class="partitura-container">
            <div id="paper"></div>
            
            <!-- Controles de reprodução -->
            <div class="controles-reproducao">
                <div class="bpm-control">
                    <label for="bpm">BPM:</label>
                    <input type="number" id="bpm" name="bpm" value="<?php echo $_SESSION['tempo']; ?>" min="40" max="240">
                    <button id="atualizar-bpm" class="btn-secundario">Atualizar</button>
                </div>
                <div class="playback-buttons">
                    <button id="play" class="btn-play"><span>▶</span> Tocar</button>
                    <button id="stop" class="btn-stop"><span>⏹</span> Parar</button>
                </div>
            </div>
        </div>

        <div class="painel-edicao">
            <div class="tabs">
                <button class="tab-btn active" data-tab="notas">Notas</button>
                <button class="tab-btn" data-tab="configuracoes">Configurações</button>
                <button class="tab-btn" data-tab="salvar">Salvar/Carregar</button>
            </div>

            <div class="tab-content">
                <!-- Painel de Notas -->
                <div id="notas" class="tab-pane active">
                    <div class="card">
                        <h3>Adicionar Notas</h3>
                        <form action="" method="post" class="form-grid">
                            <div class="form-group">
                                <label for="nota">Nota:</label>
                                <select name="nota" id="nota" class="select-estilizado">
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
                            </div>
                            <div class="form-group">
                                <label for="nota_select">Duração:</label>
                                <select name="nota_select" id="nota_select" class="select-estilizado">
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
                            </div>
                            <div class="form-group">
                                <label for="oitava">Oitava:</label>
                                <select name="oitava" id="oitava" class="select-estilizado">
                                    <option value="" selected>Padrão</option>
                                    <option value="'">1 acima</option>
                                    <option value="''">2 acima</option>
                                    <option value="'''">3 acima</option>
                                    <option value=",">1 abaixo</option>
                                    <option value=",,">2 abaixo</option>
                                    <option value=",,,">3 abaixo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-primario" style="margin-top: 2.1rem;">Adicionar Nota</button>
                            </div>
                        </form>
                    </div>

                    <div class="acoes-rapidas">
                        <form action="" method="post" class="inline-form">
                            <input type="hidden" name="ritornelo" value=":">
                            <button type="submit" class="btn-secundario">Adicionar Ritornelo</button>
                        </form>
                        
                        <form action="" method="post" class="inline-form">
                            <input type="hidden" name="retirar" value="1">
                            <button type="submit" class="btn-secundario">Retirar Última Nota</button>
                        </form>
                        
                        <form action="" method="post" class="inline-form">
                            <input type="hidden" name="limpar" value="1">
                            <button type="submit" class="btn-perigo">Limpar Todas as Notas</button>
                        </form>
                    </div>
                    
                    <div class="card">
                        <h3>Adicionar Acorde</h3>
                        <form action="" method="post" class="form-grid">
                            <div class="form-group">
                                <label for="acorde">Acorde:</label>
                                <input type="text" name="acorde" id="acorde" placeholder="Ex: C, Am, G7">
                            </div>
                            <div class="form-group">
                                <label for="numero_compasso">Compasso:</label>
                                <input type="number" name="numero_compasso" id="numero_compasso" min="1" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-primario" style="margin-top: 2.1rem;">Adicionar Acorde</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Painel de Configurações -->
                <div id="configuracoes" class="tab-pane">
                    <div class="card">
                        <h3>Tonalidade</h3>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="tom">Selecione a tonalidade:</label>
                                <select name="tom" id="tom" class="select-estilizado">
                                    <option value="C">C (Dó maior)</option>
                                    <option value="D">D (Ré maior)</option>
                                    <option value="E">E (Mi maior)</option>
                                    <option value="F">F (Fá maior)</option>
                                    <option value="G">G (Sol maior)</option>
                                    <option value="A">A (Lá maior)</option>
                                    <option value="B">B (Si maior)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primario">Alterar Tonalidade</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>Fórmula de Compasso</h3>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="compasso">Selecione o compasso:</label>
                                <select name="compasso" id="compasso" class="select-estilizado">
                                    <option value="4/4">4/4 (Quaternário)</option>
                                    <option value="3/4">3/4 (Ternário)</option>
                                    <option value="2/4">2/4 (Binário)</option>
                                    <option value="1/4">1/4</option>
                                    <option value="2/3">2/3</option>
                                    <option value="2/2">2/2 (Alla breve)</option>
                                    <option value="3/8">3/8</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primario">Alterar Compasso</button>
                        </form>
                    </div>
                </div>

                <!-- Painel de Salvar/Carregar -->
                <div id="salvar" class="tab-pane">
                    <div class="card">
                        <h3>Salvar Música</h3>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="nome_musica">Nome da música:</label>
                                <input type="text" name="nome_musica" id="nome_musica" required placeholder="Digite o nome da música">
                            </div>
                            <button type="submit" name="salvar_musica" class="btn-primario">Salvar Música</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>Carregar Música</h3>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="carregar_musica">Selecione uma música:</label>
                                <select name="carregar_musica" id="carregar_musica" class="select-estilizado">
                                    <?php
                                    foreach($availableMusics as $musica) {
                                        echo "<option value='$musica'>$musica</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn-primario">Carregar Música</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© <?php echo date('Y'); ?> Editor de Partituras Musicais</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-basic.min.js"></script>
    <script>
        // Dados da música em formato ABC
        const musicaABC = `<?php echo $abcNotation; ?>`;

        document.addEventListener("DOMContentLoaded", function () {
            // Renderiza a partitura na tela
            let visualObj = ABCJS.renderAbc("paper", musicaABC, { 
                responsive: "resize",
                add_classes: true,
                staffwidth: 800
            })[0];

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
                        millisecondsPerMeasure: bpm
                    });

                    await synthControl.prime();
                    synthControl.start();
                    document.getElementById("play").classList.add("playing");
                    document.getElementById("stop").classList.remove("disabled");
                } catch (error) {
                    console.error("Erro ao tocar a música:", error);
                }
            }

            function stopMusic() {
                if (synthControl) {
                    synthControl.stop();
                    document.getElementById("play").classList.remove("playing");
                    document.getElementById("stop").classList.add("disabled");
                }
            }

            document.getElementById("play").addEventListener("click", playMusic);
            document.getElementById("stop").addEventListener("click", stopMusic);
            document.getElementById("atualizar-bpm").addEventListener("click", function(e) {
                e.preventDefault();
                // Aqui você pode adicionar código para atualizar o BPM via AJAX se desejar
                // Ou simplesmente recarregar a página com o novo valor
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bpm';
                input.value = document.getElementById('bpm').value;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            });

            // Gerenciamento de abas
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove a classe active de todos os botões e painéis
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    // Adiciona a classe active ao botão clicado e ao painel correspondente
                    button.classList.add('active');
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Fechar mensagens de erro/sucesso
            const botoesFechamento = document.querySelectorAll('.fechar-mensagem');
            botoesFechamento.forEach(botao => {
                botao.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>