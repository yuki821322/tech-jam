document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-button');
    const formSections = document.querySelectorAll('.form-section');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // ボタン切り替え
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // フォーム切り替え
            const target = button.getAttribute('data-target');
            formSections.forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(target).classList.add('active');
        });
    });
});
