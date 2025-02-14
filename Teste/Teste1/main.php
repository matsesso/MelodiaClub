<?php
header('Content-Type: application/javascript');
?>

// Renderiza a partitura inicial
window.addEventListener('load', function() {
    // Renderiza a música com as notas adicionadas
    ABCJS.renderAbc("paper", musicaABC, {
        responsive: "resize"
    });

    // Adiciona o listener para mudança de música
    document.getElementById("musicSelect").addEventListener("change", function(e) {
        const selectedMusic = MUSICAS[e.target.value];
        if (selectedMusic) {
            ABCJS.renderAbc("paper", selectedMusic, {
                responsive: "resize"
            });
        }
    });
});