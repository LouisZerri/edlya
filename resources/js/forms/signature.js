class SignaturePad {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.isDrawing = false;
        this.lastX = 0;
        this.lastY = 0;

        this.init();
    }

    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());

        // Mouse events
        this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
        this.canvas.addEventListener('mousemove', (e) => this.draw(e));
        this.canvas.addEventListener('mouseup', () => this.stopDrawing());
        this.canvas.addEventListener('mouseout', () => this.stopDrawing());

        // Touch events
        this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e));
        this.canvas.addEventListener('touchmove', (e) => this.draw(e));
        this.canvas.addEventListener('touchend', () => this.stopDrawing());

        this.ctx.strokeStyle = '#1e293b';
        this.ctx.lineWidth = 2;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';
    }

    resize() {
        const rect = this.canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        
        this.canvas.width = rect.width * dpr;
        this.canvas.height = rect.height * dpr;
        
        this.ctx.scale(dpr, dpr);
        this.ctx.strokeStyle = '#1e293b';
        this.ctx.lineWidth = 2;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';
    }

    getPosition(e) {
        const rect = this.canvas.getBoundingClientRect();
        if (e.touches) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    startDrawing(e) {
        e.preventDefault();
        this.isDrawing = true;
        const pos = this.getPosition(e);
        this.lastX = pos.x;
        this.lastY = pos.y;
    }

    draw(e) {
        if (!this.isDrawing) return;
        e.preventDefault();

        const pos = this.getPosition(e);
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastX, this.lastY);
        this.ctx.lineTo(pos.x, pos.y);
        this.ctx.stroke();

        this.lastX = pos.x;
        this.lastY = pos.y;
    }

    stopDrawing() {
        this.isDrawing = false;
    }

    clear() {
        const dpr = window.devicePixelRatio || 1;
        this.ctx.clearRect(0, 0, this.canvas.width / dpr, this.canvas.height / dpr);
    }

    isEmpty() {
        const pixelData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height).data;
        for (let i = 3; i < pixelData.length; i += 4) {
            if (pixelData[i] !== 0) return false;
        }
        return true;
    }

    toDataURL() {
        return this.canvas.toDataURL('image/png');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Signature Bailleur
    const canvasBailleur = document.getElementById('signature-bailleur');
    if (canvasBailleur) {
        const padBailleur = new SignaturePad(canvasBailleur);
        
        document.getElementById('clear-bailleur')?.addEventListener('click', () => padBailleur.clear());
        
        document.getElementById('form-bailleur')?.addEventListener('submit', function(e) {
            if (padBailleur.isEmpty()) {
                e.preventDefault();
                alert('La signature est requise.');
                return;
            }
            document.getElementById('input-signature-bailleur').value = padBailleur.toDataURL();
        });
    }

    // Signature Locataire
    const canvasLocataire = document.getElementById('signature-locataire');
    if (canvasLocataire) {
        const padLocataire = new SignaturePad(canvasLocataire);
        
        document.getElementById('clear-locataire')?.addEventListener('click', () => padLocataire.clear());
        
        document.getElementById('form-locataire')?.addEventListener('submit', function(e) {
            if (padLocataire.isEmpty()) {
                e.preventDefault();
                alert('La signature est requise.');
                return;
            }
            document.getElementById('input-signature-locataire').value = padLocataire.toDataURL();
        });
    }
});