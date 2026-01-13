document.addEventListener('DOMContentLoaded', function() {
    // Gestion des labels de fichiers pour les compteurs
    const fileInputs = document.querySelectorAll('.compteur-file-input');

    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const type = this.dataset.type;
            const label = document.querySelector(`.compteur-file-label[data-type="${type}"]`);
            
            if (label && this.files.length > 0) {
                label.textContent = this.files[0].name;
            }
        });
    });

    // Gestion des labels pour les photos des pièces
    const pieceFileInputs = document.querySelectorAll('.file-input');
    
    pieceFileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const label = this.closest('label').querySelector('.file-label');
            
            if (label && this.files.length > 0) {
                label.textContent = this.files[0].name;
            }
        });
    });
});