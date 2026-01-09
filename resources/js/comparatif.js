document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const elements = document.querySelectorAll('.element-item');
    const pieceBlocks = document.querySelectorAll('.piece-block');

    if (filterBtns.length === 0) return;

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Update active button
            filterBtns.forEach(b => {
                b.classList.remove('bg-slate-800', 'text-white');
                b.classList.add('hover:bg-slate-50');
            });
            this.classList.add('bg-slate-800', 'text-white');
            this.classList.remove('hover:bg-slate-50');

            // Filter elements
            elements.forEach(el => {
                if (filter === 'all' || el.dataset.status === filter) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });

            // Hide empty piece blocks
            pieceBlocks.forEach(block => {
                const visibleElements = block.querySelectorAll('.element-item:not(.hidden)');
                if (visibleElements.length === 0) {
                    block.classList.add('hidden');
                } else {
                    block.classList.remove('hidden');
                }
            });
        });
    });
});