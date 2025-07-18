window.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById("login-modal");
  const openBtn = document.getElementById("open-login");
  const closeBtn = document.querySelector('.close-btn');
  const urlParams = new URLSearchParams(window.location.search);
  const isError = urlParams.get('error') === '1';

  // エラーがあれば自動的にモーダルを表示
  if (isError) {
    modal.style.display = 'flex'; // モーダル中央に表示する場合は 'flex'
  }

  // ログインボタンクリックでモーダル表示
  openBtn.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  // 閉じるボタンクリックで非表示
  closeBtn?.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // 背景クリックでモーダル閉じる
  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});
