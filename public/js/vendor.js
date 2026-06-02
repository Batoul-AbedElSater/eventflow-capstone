document.querySelectorAll('.category_btn button').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.category_btn button').forEach(b => {
            b.className = 'fltr_btn';
        });
        this.className = 'fltr_btn_active';

        const category = this.dataset.category;
        document.querySelectorAll('.vendor_card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });
});

document.getElementById('search_bar').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.vendor_card').forEach(card => {
        const name = card.querySelector('.vendor_name').textContent.toLowerCase();
        card.classList.toggle('hidden', !name.includes(query));
    });
});

document.querySelectorAll('.card_heart_btn').forEach(btn => {
    btn.addEventListener('click', function () {
        this.classList.toggle('liked');
        const icon = this.querySelector('i');
        icon.classList.toggle('far');
        icon.classList.toggle('fas');
    });
});
