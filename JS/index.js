const modal = document.getElementById("login-modal");
const openBtn = document.getElementById("open-login");
const closeOnBackgroundClick = (e) => {
if (e.target === modal) {
    modal.style.display = "none";
}
};

openBtn.addEventListener("click", () => {
modal.style.display = "flex"; // ← flex にすると中央表示される
});

window.addEventListener("click", closeOnBackgroundClick);

// 追加で閉じるボタン処理も入れる場合
document.querySelector('.close-btn')?.addEventListener('click', () => {
  modal.style.display = 'none';
});