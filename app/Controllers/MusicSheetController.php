<?php

namespace App\Controllers;

class MusicSheetController {
    private $notesFile = "Txts/notas.txt";
    private $keyFile = "Txts/tom.txt";
    private $timeSignatureFile = "Txts/compasso.txt";
    private $tempoFile = "Txts/tempo.txt";
    private $musicFolder = "Musicas";

    public function __construct() {
        $this->initializeFiles();
        $this->initializeSession();
    }

    private function initializeFiles() {
        if (!file_exists($this->musicFolder)) {
            mkdir($this->musicFolder, 0777, true);
        }
        if (!file_exists($this->notesFile) || filesize($this->notesFile) == 0) {
            file_put_contents($this->notesFile, "");
        }
        if (!file_exists($this->keyFile)) {
            file_put_contents($this->keyFile, "C");
        }
    }

    private function initializeSession() {
        if (!isset($_SESSION['quantidadeDeCompassos'])) {
            $_SESSION['quantidadeDeCompassos'] = 0;
        }
        if (!isset($_SESSION['quebraDeLinha'])) {
            $_SESSION['quebraDeLinha'] = 0;
        }
    }

    public function clearSheet() {
        file_put_contents($this->notesFile, "");
        file_put_contents($this->keyFile, "C");
        $_SESSION['quantidadeDeCompassos'] = 0;
        $_SESSION['quebraDeLinha'] = 0;
    }

    public function removeLastNote() {
        $content = file_get_contents($this->notesFile);
        
        $count = 0;
        $newContent = preg_replace_callback('/[^ |\n]|\|/', function ($match) use (&$count) {
            return (++$count > 2) ? $match[0] : '';
        }, strrev($content));

        file_put_contents($this->notesFile, strrev($newContent));
    }

    public function updateTimeSignature($timeSignature) {
        file_put_contents($this->timeSignatureFile, $timeSignature);
        list($numerator, $denominator) = explode('/', $timeSignature);
        $_SESSION['totalTemposCompasso'] = (float)$numerator;
        $_SESSION['quantidadeDeCompassos'] = 0;
    }

    public function addChord($chord, $measureNumber) {
        $content = file_get_contents($this->notesFile);
        $measures = explode("|", $content);
        
        if($measureNumber > 0 && $measureNumber <= count($measures)) {
            $index = $measureNumber - 1;
            $measures[$index] = preg_replace('/"[^"]*"/', '', $measures[$index]);
            $measures[$index] = ' "' . $chord . '" ' . trim($measures[$index]);
            
            $newContent = implode("|", $measures);
            file_put_contents($this->notesFile, $newContent);
            return true;
        }
        return false;
    }

    public function updateTempo($bpm) {
        file_put_contents($this->tempoFile, $bpm);
    }

    public function updateKey($key) {
        file_put_contents($this->keyFile, $key);
    }

    public function addRitornello($ritornello) {
        file_put_contents($this->notesFile, $ritornello, FILE_APPEND);
    }

    public function addNote($note, $octave, $duration) {
        if (empty($note)) return false;

        $noteValue = $this->processNote($note, $octave, $duration);
        if (!$noteValue) return false;

        $timeSignature = file_get_contents($this->timeSignatureFile);
        list($numerator, $denominator) = explode('/', $timeSignature);
        $totalBeatsPerMeasure = (float)$numerator;

        $file = fopen($this->notesFile, "a");
        if (!$file) return false;

        $this->writeNoteToFile($file, $noteValue, $totalBeatsPerMeasure);
        fclose($file);
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
        return $numerator / $denominator;
    }

    public function saveMusic($musicName) {
        $musicName = preg_replace("/[^a-zA-Z0-9]/", "_", $musicName);
        
        $musicData = [
            'notas' => file_get_contents($this->notesFile),
            'tom' => file_get_contents($this->keyFile),
            'compasso' => file_get_contents($this->timeSignatureFile),
            'bpm' => file_get_contents($this->tempoFile)
        ];

        $musicFile = "{$this->musicFolder}/{$musicName}.txt";
        return file_put_contents($musicFile, json_encode($musicData)) !== false;
    }

    public function loadMusic($musicName) {
        $musicFile = "{$this->musicFolder}/{$musicName}.txt";
        
        if (!file_exists($musicFile)) {
            return false;
        }

        $musicData = json_decode(file_get_contents($musicFile), true);
        if (!$musicData) {
            return false;
        }

        file_put_contents($this->notesFile, $musicData['notas']);
        file_put_contents($this->keyFile, $musicData['tom']);
        file_put_contents($this->timeSignatureFile, $musicData['compasso']);
        
        $_SESSION['quantidadeDeCompassos'] = 0;
        $_SESSION['quebraDeLinha'] = 0;

        return true;
    }

    public function getAvailableMusics() {
        return glob("{$this->musicFolder}/*.txt");
    }

    private function writeNoteToFile($file, $noteValue, $totalBeatsPerMeasure) {
        $availableSpace = $totalBeatsPerMeasure - $_SESSION['quantidadeDeCompassos'];
        
        if ($noteValue['value'] > $availableSpace) {
            $remainingTime = $noteValue['value'];
            
            // Primeira parte - completa o compasso atual
            if ($availableSpace > 0) {
                fwrite($file, $noteValue['note'] . $availableSpace . "-");
                $remainingTime -= $availableSpace;
                fwrite($file, " | ");
                $_SESSION['quebraDeLinha']++;
                if($_SESSION['quebraDeLinha'] >= 5){
                    fwrite($file, "\\n");
                    $_SESSION['quebraDeLinha'] = 0;
                }
            }
            
            // Compassos completos intermediários
            while ($remainingTime > $totalBeatsPerMeasure) {
                fwrite($file, "-" . $noteValue['note'] . $totalBeatsPerMeasure . "-");
                $remainingTime -= $totalBeatsPerMeasure;
                fwrite($file, " | ");
                $_SESSION['quebraDeLinha']++;
                if($_SESSION['quebraDeLinha'] >= 5){
                    fwrite($file, "\\n");
                    $_SESSION['quebraDeLinha'] = 0;
                }
            }
            
            // Última parte - resto que sobrou
            if ($remainingTime > 0) {
                fwrite($file, "-" . $noteValue['note'] . $remainingTime);
                $_SESSION['quantidadeDeCompassos'] = $remainingTime;
            } else {
                $_SESSION['quantidadeDeCompassos'] = 0;
            }
            
        } else {
            // Nota cabe no compasso atual
            if(isset($noteValue['duration']) && $noteValue['duration'] != ".") {
                fwrite($file, $noteValue['note'] . $noteValue['duration']);
                $_SESSION['quantidadeDeCompassos'] += $noteValue['value'];
                
                // Se completou exatamente o compasso
                if(abs($_SESSION['quantidadeDeCompassos'] - $totalBeatsPerMeasure) < 0.0001){
                    fwrite($file, " | ");
                    $_SESSION['quantidadeDeCompassos'] = 0;
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        fwrite($file, "\\n");
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
            } else {
                fwrite($file, $noteValue['note']);
                $_SESSION['quantidadeDeCompassos'] += $noteValue['value'];
                if(abs($_SESSION['quantidadeDeCompassos'] - $totalBeatsPerMeasure) < 0.0001){
                    fwrite($file, " | ");
                    $_SESSION['quantidadeDeCompassos'] = 0;
                    $_SESSION['quebraDeLinha']++;
                    if($_SESSION['quebraDeLinha'] >= 5){
                        fwrite($file, "\\n");
                        $_SESSION['quebraDeLinha'] = 0;
                    }
                }
            }
        }
    }

    public static function validation ($data) {
        $musicSheet = new MusicSheetController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($data['limpar'])) {
                $musicSheet->clearSheet();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['retirar'])) {
                $musicSheet->removeLastNote();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['compasso'])) {
                $musicSheet->updateTimeSignature($data['compasso']);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['acorde']) && isset($data['numero_compasso'])) {
                $success = $musicSheet->addChord($data['acorde'], (int)$data['numero_compasso']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Acorde adicionado ao compasso {$data['numero_compasso']}!" 
                    : "Número de compasso inválido!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['bpm'])) {
                $musicSheet->updateTempo($data['bpm']);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['tom'])) {
                $musicSheet->updateKey($data['tom']);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['nota'])) {
                $success = $musicSheet->addNote($data['nota'], $data['oitava'], $data['nota_select']);
                if ($success) {
                    $_SESSION['sucesso'] = "Nota adicionada com sucesso!";
                }
                header("Location:/MelodiaClub/index.php");
                exit();
            }
        
            if (isset($data['salvar_musica']) && isset($data['nome_musica'])) {
                $success = $musicSheet->saveMusic($data['nome_musica']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Música '{$data['nome_musica']}' salva com sucesso!" 
                    : "Erro ao salvar a música!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        
            if (isset($data['carregar_musica'])) {
                $success = $musicSheet->loadMusic($data['carregar_musica']);
                $_SESSION[$success ? 'sucesso' : 'erro'] = $success 
                    ? "Música '{$data['carregar_musica']}' carregada com sucesso!" 
                    : "Erro ao carregar a música!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            if(isset($data['ritornelo'])) {
                $ritornelo = $data['ritornelo'];
                file_put_contents($musicSheet->notesFile, $ritornelo, FILE_APPEND);
            }
        }
    }

} 

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    MusicSheetController::validation($_POST);
}

