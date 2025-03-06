<?php

namespace MelodiaClub\controllers;

class MusicSheetController {
    
    public function __construct() {
        $this->initializeSession();
    }

    private function initializeSession() {
        if (!isset($_SESSION['notas'])) {
            $_SESSION['notas'] = [];
        }
        if (!isset($_SESSION['tom'])) {
            $_SESSION['tom'] = "C";
        }
        if (!isset($_SESSION['compasso'])) {
            $_SESSION['compasso'] = "4/4";
        }
        if (!isset($_SESSION['tempo'])) {
            $_SESSION['tempo'] = "60";
        }
        if (!isset($_SESSION['quantidadeDeCompassos'])) {
            $_SESSION['quantidadeDeCompassos'] = 0;
        }
        if (!isset($_SESSION['quebraDeLinha'])) {
            $_SESSION['quebraDeLinha'] = 0;
        }
        if (!isset($_SESSION['musicas'])) {
            $_SESSION['musicas'] = [];
        }
        if (!isset($_SESSION['notas_ligadas'])) {
            $_SESSION['notas_ligadas'] = [];
        }
    }

    public function clearSheet() {
        $_SESSION['notas'] = [];
        $_SESSION['tom'] = "C";
        $_SESSION['quantidadeDeCompassos'] = 0;
        $_SESSION['quebraDeLinha'] = 0;
        $_SESSION['notas_ligadas'] = [];
    }

    public function removeLastNote() {
        if (count($_SESSION['notas']) > 0) {
            $last_note = array_pop($_SESSION['notas']);
            
            // Verifica se a nota removida tinha ligadura
            if (strpos($last_note, '-') !== false) {
                // Se tinha ligadura, verifica se há outras ligaduras para remover
                $linked_notes = array_keys($_SESSION['notas_ligadas'], true);
                if (!empty($linked_notes)) {
                    $last_linked = end($linked_notes);
                    if ($last_linked) {
                        unset($_SESSION['notas_ligadas'][$last_linked]);
                    }
                }
            }
            
            // Se removeu uma nota que completa um compasso (a barra |), atualiza a contagem
            if ($last_note === '|') {
                // Recalcula a quantidade de compassos
                $this->recalcularCompassos();
            }
        }
    }

    private function recalcularCompassos() {
        // Obtém o valor do compasso atual
        list($numerator, $denominator) = explode('/', $_SESSION['compasso']);
        $totalBeatsPerMeasure = (float)$numerator;
        
        // Reconta os tempos no compasso atual
        $_SESSION['quantidadeDeCompassos'] = 0;
        $count = 0;
        
        foreach (array_reverse($_SESSION['notas']) as $nota) {
            if ($nota === '|') {
                break;
            }
            
            // Extrai a duração da nota (formato: nota + duração, ex: C4, D1/2)
            if (preg_match('/[A-Ga-g\^_=z][\',]*(\d+|\d+\/\d+)/', $nota, $matches)) {
                if (isset($matches[1])) {
                    $duration = $matches[1];
                    if (strpos($duration, '/') !== false) {
                        list($num, $denom) = explode('/', $duration);
                        $value = $num / $denom;
                    } else {
                        $value = (float)$duration;
                    }
                    $count += $value;
                }
            }
        }
        
        $_SESSION['quantidadeDeCompassos'] = $count;
    }

    public function updateTimeSignature($timeSignature) {
        $_SESSION['compasso'] = $timeSignature;
        list($numerator, $denominator) = explode('/', $timeSignature);
        $_SESSION['totalTemposCompasso'] = (float)$numerator;
        
        // Recalcula todos os compassos quando mudar a fórmula de compasso
        $this->reorganizeNotesForNewTimeSignature((float)$numerator);
    }

    private function reorganizeNotesForNewTimeSignature($newBeatsPerMeasure) {
        // Implementação simplificada: limpa as notas e começa de novo
        // Em uma implementação mais avançada, você poderia reorganizar as notas existentes
        $_SESSION['quantidadeDeCompassos'] = 0;
        $_SESSION['quebraDeLinha'] = 0;
        
        // Opcionalmente, você pode querer preservar as notas, mas reorganizá-las
        // nos novos compassos. Esta seria uma implementação mais complexa.
    }

    public function addChord($chord, $measureNumber) {
        $notasTexto = implode(" ", $_SESSION['notas']);
        $measures = explode("|", $notasTexto);
        
        if($measureNumber > 0 && $measureNumber <= count($measures)) {
            $index = $measureNumber - 1;
            $measures[$index] = preg_replace('/"[^"]*"/', '', $measures[$index]);
            $measures[$index] = ' "' . $chord . '" ' . trim($measures[$index]);
            
            $newContent = implode("|", $measures);
            $_SESSION['notas'] = explode(" ", $newContent);
            return true;
        }
        return false;
    }

    public function updateTempo($bpm) {
        $_SESSION['tempo'] = $bpm;
    }

    public function updateKey($key) {
        $_SESSION['tom'] = $key;
    }

    public function addRitornello() {
        $_SESSION['notas'][] = "|:";
    }

    public function addNote($note, $octave, $duration) {
        if (empty($note)) return false;

        $noteValue = $this->processNote($note, $octave, $duration);
        if (!$noteValue) return false;

        $timeSignature = $_SESSION['compasso'];
        list($numerator, $denominator) = explode('/', $timeSignature);
        $totalBeatsPerMeasure = (float)$numerator;

        $this->addNoteToSession($noteValue, $totalBeatsPerMeasure);
        return true;
    }

    private function processNote($note, $octave, $duration) {
        if (!empty($octave)) {
            $note .= $octave;
        }

        if ($duration === ".") {
            return ["note" => "." . $note, "value" => 1];
        }

        $value = strpos($duration, '/') !== false 
            ? $this->convertFractionToDecimal($duration)
            : (float)$duration;

        return ["note" => $note, "duration" => $duration, "value" => $value];
    }

    private function convertFractionToDecimal($fraction) {
        list($numerator, $denominator) = explode('/', $fraction);
        return (float)$numerator / (float)$denominator;
    }

    public function saveMusic($musicName) {
        $musicName = preg_replace("/[^a-zA-Z0-9]/", "_", $musicName);
        
        $musicData = [
            'notas' => $_SESSION['notas'],
            'tom' => $_SESSION['tom'],
            'compasso' => $_SESSION['compasso'],
            'tempo' => $_SESSION['tempo'],
            'notas_ligadas' => $_SESSION['notas_ligadas']
        ];

        $_SESSION['musicas'][$musicName] = $musicData;
        return true;
    }

    public function loadMusic($musicName) {
        if (!isset($_SESSION['musicas'][$musicName])) {
            return false;
        }

        $musicData = $_SESSION['musicas'][$musicName];
        
        $_SESSION['notas'] = $musicData['notas'];
        $_SESSION['tom'] = $musicData['tom'];
        $_SESSION['compasso'] = $musicData['compasso'];
        $_SESSION['tempo'] = $musicData['tempo'];
        $_SESSION['notas_ligadas'] = isset($musicData['notas_ligadas']) ? $musicData['notas_ligadas'] : [];
        
        $_SESSION['quantidadeDeCompassos'] = 0;
        $_SESSION['quebraDeLinha'] = 0;
        
        // Recalcula o valor de quantidadeDeCompassos baseado nas notas carregadas
        $this->recalcularTemposNoCompassoAtual();

        return true;
    }

    private function recalcularTemposNoCompassoAtual() {
        $beatsInCurrentMeasure = 0;
        $isCountingCurrentMeasure = true;
        
        // Percorre as notas de trás para frente
        for ($i = count($_SESSION['notas']) - 1; $i >= 0; $i--) {
            $nota = $_SESSION['notas'][$i];
            
            // Se encontrar um separador de compasso, para de contar
            if ($nota === '|' && $isCountingCurrentMeasure) {
                $isCountingCurrentMeasure = false;
                continue;
            }
            
            // Se já encontrou um separador, então já não está mais no compasso atual
            if (!$isCountingCurrentMeasure) {
                continue;
            }
            
            // Extrai a duração da nota
            if (preg_match('/[A-Ga-g\^_=z][\',]*(\d+|\d+\/\d+)/', $nota, $matches)) {
                if (isset($matches[1])) {
                    $duration = $matches[1];
                    if (strpos($duration, '/') !== false) {
                        list($num, $denom) = explode('/', $duration);
                        $value = (float)$num / (float)$denom;
                    } else {
                        $value = (float)$duration;
                    }
                    $beatsInCurrentMeasure += $value;
                }
            }
        }
        
        $_SESSION['quantidadeDeCompassos'] = $beatsInCurrentMeasure;
    }

    public function getAvailableMusics() {
        return array_keys($_SESSION['musicas']);
    }

    private function addNoteToSession($noteValue, $totalBeatsPerMeasure) {
        $availableSpace = $totalBeatsPerMeasure - $_SESSION['quantidadeDeCompassos'];
        
        // Para durações menores que 1, precisamos usar notação ABC adequada
        // Se a duração for menor que 1 e não estiver em formato de fração, converter
        $needsTie = false;
        
        if ($noteValue['value'] > $availableSpace) {
            $remainingTime = $noteValue['value'];
            $notaIndex = count($_SESSION['notas']);
            
            // Primeira parte - completa o compasso atual
            if ($availableSpace > 0) {
                // Adiciona a nota com a duração que cabe no compasso
                $formattedDuration = $this->formatDuration($availableSpace);
                $_SESSION['notas'][] = $noteValue['note'] . $formattedDuration . "-";
                $_SESSION['notas_ligadas'][$notaIndex] = true;
                
                $remainingTime -= $availableSpace;
                $_SESSION['notas'][] = "|";
                $_SESSION['quebraDeLinha']++;
                if($_SESSION['quebraDeLinha'] >= 5){
                    $_SESSION['notas'][] = "\\n";
                    $_SESSION['quebraDeLinha'] = 0;
                }
            }
            
            // Compassos completos intermediários
            while ($remainingTime > $totalBeatsPerMeasure) {
                $notaIndex = count($_SESSION['notas']);
                $_SESSION['notas'][] = "-" . $noteValue['note'] . $totalBeatsPerMeasure . "-";
                $_SESSION['notas_ligadas'][$notaIndex] = true;
                
                $remainingTime -= $totalBeatsPerMeasure;
                $_SESSION['notas'][] = "|";
                $_SESSION['quebraDeLinha']++;
                if($_SESSION['quebraDeLinha'] >= 5){
                    $_SESSION['notas'][] = "\\n";
                    $_SESSION['quebraDeLinha'] = 0;
                }
            }
            
            // Última parte - resto que sobrou
            if ($remainingTime > 0) {
                $formattedDuration = $this->formatDuration($remainingTime);
                $notaIndex = count($_SESSION['notas']);
                $_SESSION['notas'][] = "-" . $noteValue['note'] . $formattedDuration;
                $_SESSION['quantidadeDeCompassos'] = $remainingTime;
            } else {
                $_SESSION['quantidadeDeCompassos'] = 0;
            }
            
        } else {
            // Nota cabe no compasso atual
            if(isset($noteValue['duration']) && $noteValue['duration'] != ".") {
                $formattedDuration = $noteValue['duration'];
                $_SESSION['notas'][] = $noteValue['note'] . $formattedDuration;
                $_SESSION['quantidadeDeCompassos'] += $noteValue['value'];
                
                // Se completou exatamente o compasso
                if(abs($_SESSION['quantidadeDeCompassos'] - $totalBeatsPerMeasure) < 0.0001){
                    $_SESSION['notas'][] = "|";
                    $_SESSION['quantidadeDeCompassos'] = 0;
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        $_SESSION['notas'][] = "\\n";
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
            } else {
                $_SESSION['notas'][] = $noteValue['note'];
                $_SESSION['quantidadeDeCompassos'] += $noteValue['value'];
                if(abs($_SESSION['quantidadeDeCompassos'] - $totalBeatsPerMeasure) < 0.0001){
                    $_SESSION['notas'][] = "|";
                    $_SESSION['quantidadeDeCompassos'] = 0;
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        $_SESSION['notas'][] = "\\n";
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
            }
        }
    }

    // Função auxiliar para formatar durações de maneira adequada
    private function formatDuration($duration) {
        // Se for um número inteiro, retorna como está
        if (floor($duration) == $duration) {
            return (string)$duration;
        }
        
        // Tenta representar como fração
        // Para simplificar, usamos denominadores comuns: 2, 4, 8, 16, 32
        $denominators = [2, 4, 8, 16, 32];
        foreach ($denominators as $denominator) {
            $numerator = $duration * $denominator;
            if (floor($numerator) == $numerator) {
                return $numerator . '/' . $denominator;
            }
        }
        
        // Se não conseguir representar exatamente, arredonda para múltiplo de 1/32
        $numerator = round($duration * 32);
        return $numerator . '/32';
    }

    public function getAbcNotation() {
        $abc_notas = implode(" ", $_SESSION['notas']);
        
        return "X:1\nT:Suas Notas\nM:{$_SESSION['compasso']}\nL:1/4\nQ:{$_SESSION['tempo']}\nK:{$_SESSION['tom']}\n|$abc_notas|";
    }

    public static function validation($data) {
        $musicSheet = new MusicSheetController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($data['limpar'])) {
                $musicSheet->clearSheet();
                header("Location:/index");
                exit();
            }
        
            if (isset($data['retirar'])) {
                $musicSheet->removeLastNote();
                header("Location:/index");
                exit();
            }
        
            if (isset($data['compasso'])) {
                $musicSheet->updateTimeSignature($data['compasso']);
                header("Location:/index");
                exit();
            }
        
            if (isset($data['acorde']) && isset($data['numero_compasso'])) {
                $success = $musicSheet->addChord($data['acorde'], (int)$data['numero_compasso']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Acorde adicionado ao compasso {$data['numero_compasso']}!" 
                    : "Número de compasso inválido!";
                header("Location:/index");
                exit();
            }
        
            if (isset($data['bpm'])) {
                $musicSheet->updateTempo($data['bpm']);
                header("Location:/index");
                exit();
            }
        
            if (isset($data['tom'])) {
                $musicSheet->updateKey($data['tom']);
                header("Location:/index");
                exit();
            }
        
            if (isset($data['nota'])) {
                $success = $musicSheet->addNote($data['nota'], $data['oitava'], $data['nota_select']);
                if ($success) {
                    $_SESSION['sucesso'] = "Nota adicionada com sucesso!";
                }
                header("Location:/index");
                exit();
            }
        
            if (isset($data['salvar_musica']) && isset($data['nome_musica'])) {
                $success = $musicSheet->saveMusic($data['nome_musica']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Música '{$data['nome_musica']}' salva com sucesso!" 
                    : "Erro ao salvar a música!";
                header("Location:/index");
                exit();
            }
        
            if (isset($data['carregar_musica'])) {
                $success = $musicSheet->loadMusic($data['carregar_musica']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Música '{$data['carregar_musica']}' carregada com sucesso!" 
                    : "Erro ao carregar a música!";
                header("Location:/index");
                exit();
            }

            if(isset($data['ritornelo'])) {
                $musicSheet->addRitornello();
                header("Location:/index");
                exit();
            }
        }
    }
}
