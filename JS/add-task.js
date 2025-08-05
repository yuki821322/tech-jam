const buttons = document.querySelectorAll('.tab-button');
const sections = document.querySelectorAll('.form-section');

buttons.forEach(button => {
    button.addEventListener('click', () => {
        // タブボタン切り替え
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        // 対象フォーム表示
        const targetId = button.getAttribute('data-target');
        sections.forEach(section => {
            section.classList.toggle('active', section.id === targetId);
        });
    });
});