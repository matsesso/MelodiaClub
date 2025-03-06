<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodiaClub - Editor de Partituras Musicais</title>
    <!-- ABCJS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-audio.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/styles.css">
    <style>

    </style>
</head>
<body class="bg-light">

    <div class="container py-4">
        <header class="pb-2 mb-3 border-bottom">
            <h3 class="fw-bold text-center text-success">MelodiaClub - Editor de Partituras Musicais</h3>
        </header>

        <!-- Área da partitura -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div id="paper" class="bg-white rounded border"></div>
                
                <!-- Controles de reprodução -->
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">BPM:</span>
                            <input type="number" id="bpm" name="bpm" value="<?php echo $_SESSION['tempo']; ?>" min="40" max="240" class="form-control">
                            <button id="atualizar-bpm" class="btn btn-outline-secondary">Atualizar</button>
                        </div>
                    </div>
                    <div class="col-md-9 text-md-end mt-3 mt-md-0">
                        <button id="play" class="btn btn-success me-2">
                            <i class="bi bi-play-fill"></i> Tocar
                        </button>
                        <button id="stop" class="btn btn-danger">
                            <i class="bi bi-stop-fill"></i> Parar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagens de erro/sucesso -->
        <?php if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>' . $_SESSION['erro'] . 
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['sucesso'] . 
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            unset($_SESSION['sucesso']);
        } ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="notas-tab" data-bs-toggle="tab" data-bs-target="#notas" type="button" role="tab" aria-controls="notas" aria-selected="true">Notas</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="configuracoes-tab" data-bs-toggle="tab" data-bs-target="#configuracoes" type="button" role="tab" aria-controls="configuracoes" aria-selected="false">Configurações</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salvar-tab" data-bs-toggle="tab" data-bs-target="#salvar" type="button" role="tab" aria-controls="salvar" aria-selected="false">Salvar/Carregar</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Painel de Notas -->
                            <div id="notas" class="tab-pane active" role="tabpanel" aria-labelledby="notas-tab">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Adicionar Notas</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="nota" class="form-label">Nota:</label>
                                                    <select name="nota" id="nota" class="form-select">
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
                                                <div class="col-md-3 mb-3">
                                                    <label for="nota_select" class="form-label">Duração:</label>
                                                    <select name="nota_select" id="nota_select" class="form-select">
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
                                                <div class="col-md-3 mb-3">
                                                    <label for="oitava" class="form-label">Oitava:</label>
                                                    <select name="oitava" id="oitava" class="form-select">
                                                        <option value="" selected>Padrão</option>
                                                        <option value="'">1 acima</option>
                                                        <option value="''">2 acima</option>
                                                        <option value="'''">3 acima</option>
                                                        <option value=",">1 abaixo</option>
                                                        <option value=",,">2 abaixo</option>
                                                        <option value=",,,">3 abaixo</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                                    <button type="submit" class="btn btn-primary w-100">Adicionar Nota</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="ritornelo" value=":">
                                        <button type="submit" class="btn btn-outline-secondary">Adicionar Ritornelo</button>
                                    </form>
                                    
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="retirar" value="1">
                                        <button type="submit" class="btn btn-outline-secondary">Retirar Última Nota</button>
                                    </form>
                                    
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="limpar" value="1">
                                        <button type="submit" class="btn btn-outline-danger">Limpar Todas as Notas</button>
                                    </form>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Adicionar Acorde</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <div class="row">
                                                <div class="col-md-5 mb-3">
                                                    <label for="acorde" class="form-label">Acorde:</label>
                                                    <input type="text" name="acorde" id="acorde" class="form-control" placeholder="Ex: C, Am, G7">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="numero_compasso" class="form-label">Compasso:</label>
                                                    <input type="number" name="numero_compasso" id="numero_compasso" class="form-control" min="1" required>
                                                </div>
                                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                                    <button type="submit" class="btn btn-primary w-100">Adicionar Acorde</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Painel de Configurações -->
                            <div id="configuracoes" class="tab-pane" role="tabpanel" aria-labelledby="configuracoes-tab">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Tonalidade</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="" method="post">
                                                    <div class="mb-3">
                                                        <label for="tom" class="form-label">Selecione a tonalidade:</label>
                                                        <select name="tom" id="tom" class="form-select">
                                                            <option value="C">C (Dó maior)</option>
                                                            <option value="D">D (Ré maior)</option>
                                                            <option value="E">E (Mi maior)</option>
                                                            <option value="F">F (Fá maior)</option>
                                                            <option value="G">G (Sol maior)</option>
                                                            <option value="A">A (Lá maior)</option>
                                                            <option value="B">B (Si maior)</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Alterar Tonalidade</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Fórmula de Compasso</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="" method="post">
                                                    <div class="mb-3">
                                                        <label for="compasso" class="form-label">Selecione o compasso:</label>
                                                        <select name="compasso" id="compasso" class="form-select">
                                                            <option value="4/4">4/4 (Quaternário)</option>
                                                            <option value="3/4">3/4 (Ternário)</option>
                                                            <option value="2/4">2/4 (Binário)</option>
                                                            <option value="1/4">1/4</option>
                                                            <option value="2/3">2/3</option>
                                                            <option value="2/2">2/2 (Alla breve)</option>
                                                            <option value="3/8">3/8</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Alterar Compasso</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Painel de Salvar/Carregar -->
                            <div id="salvar" class="tab-pane" role="tabpanel" aria-labelledby="salvar-tab">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Salvar Música</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="" method="post">
                                                    <div class="mb-3">
                                                        <label for="nome_musica" class="form-label">Nome da música:</label>
                                                        <input type="text" name="nome_musica" id="nome_musica" class="form-control" required placeholder="Digite o nome da música">
                                                    </div>
                                                    <button type="submit" name="salvar_musica" class="btn btn-primary">Salvar Música</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Carregar Música</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="" method="post">
                                                    <div class="mb-3">
                                                        <label for="carregar_musica" class="form-label">Selecione uma música:</label>
                                                        <select name="carregar_musica" id="carregar_musica" class="form-select">
                                                            <?php
                                                            foreach($availableMusics as $musica) {
                                                                echo "<option value='$musica'>$musica</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Carregar Música</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© <?php echo date('Y'); ?> Editor de Partituras Musicais</p>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ABCJS Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/abcjs/6.2.2/abcjs-basic.min.js"></script>
    <script>
        // Dados da música em formato ABC
        const musicaABC = `<?php echo $abcNotation; ?>`;

        document.addEventListener("DOMContentLoaded", function () {
            // Renderiza a partitura na tela com um pequeno atraso para garantir que o DOM esteja pronto
            setTimeout(function() {
                let visualObj = ABCJS.renderAbc("paper", musicaABC, { 
                    responsive: "resize",
                    add_classes: true,
                    scale: 1.0,
                    paddingright: 0,
                    paddingleft: 0,
                    paddingbottom: 10,
                    paddingtop: 10,
                    staffwidth: document.getElementById('paper').clientWidth - 30
                })[0];
                
                // Armazena o objeto visual para uso posterior
                window.visualObj = visualObj;
                
                // Inicializa o controle de áudio
                initAudio(visualObj);
                
                // Ajusta a partitura quando a janela é redimensionada
                window.addEventListener('resize', function() {
                    ABCJS.renderAbc("paper", musicaABC, { 
                        responsive: "resize",
                        add_classes: true,
                        scale: 1.0,
                        paddingright: 0,
                        paddingleft: 0,
                        paddingbottom: 10,
                        paddingtop: 10,
                        staffwidth: document.getElementById('paper').clientWidth - 30
                    });
                });
            }, 100);
            
            function initAudio(visualObj) {
                // Verifica se o navegador suporta áudio
                if (!ABCJS.synth.supportsAudio()) {
                    console.log("Seu navegador não suporta áudio no ABCJS!");
                    return;
                }

                let audioContext = new (window.AudioContext || window.webkitAudioContext)();
                let synthControl = new ABCJS.synth.CreateSynth();
                window.synthControl = synthControl;

                document.getElementById("play").addEventListener("click", playMusic);
                document.getElementById("stop").addEventListener("click", stopMusic);
                document.getElementById("stop").classList.add("disabled");
            }

            async function playMusic() {
                if (window.synthControl) window.synthControl.stop();

                // Pega a velocidade selecionada
                let bpm = parseInt(document.getElementById('bpm').value);
                let millisecondsPerMeasure = (60 / bpm) * 1000;

                try {
                    await window.synthControl.init({
                        visualObj: window.visualObj,
                        audioContext: new (window.AudioContext || window.webkitAudioContext)(),
                        millisecondsPerMeasure: millisecondsPerMeasure
                    });

                    await window.synthControl.prime();
                    window.synthControl.start();
                    document.getElementById("play").classList.add("btn-dark");
                    document.getElementById("play").classList.remove("btn-success");
                    document.getElementById("stop").classList.remove("disabled");
                } catch (error) {
                    console.error("Erro ao tocar a música:", error);
                }
            }

            function stopMusic() {
                if (window.synthControl) {
                    window.synthControl.stop();
                    document.getElementById("play").classList.remove("btn-dark");
                    document.getElementById("play").classList.add("btn-success");
                    document.getElementById("stop").classList.add("disabled");
                }
            }

            document.getElementById("atualizar-bpm").addEventListener("click", function(e) {
                e.preventDefault();
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
        });
    </script>
</body>
</html>