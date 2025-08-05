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
// テキストスライド
const title = document.querySelector('.main-title');
const note = document.querySelector('.note');

let titlePos = -1000;
let notePos = -800;

const titleSpeed = 20;
const noteSpeed = 20;

function slideInTitle() {
  titlePos += titleSpeed;
  title.style.left = titlePos + "px";

  if (titlePos < 60) {
    requestAnimationFrame(slideInTitle);
  } else {
    title.style.left = "60px";

    // 少し遅れて Note. をスライド開始（500ms後）
    setTimeout(slideInNote, 500);
  }
}

function slideInNote() {
  notePos += noteSpeed;
  note.style.left = notePos + "px";

  if (notePos < 0) {
    requestAnimationFrame(slideInNote);
  } else {
    note.style.left = "0px";
  }
}


const bars = [
    { el: document.querySelector('.hr1'), pos: -5000, speed: 25, stop: 0 },
    { el: document.querySelector('.hr2'), pos: -500, speed: 20, stop: 0 },
    { el: document.querySelector('.hr3'), pos: -500, speed: 15, stop: 0 }
  ];

  function slideBar(index) {
    if (!bars[index]) return;

    const bar = bars[index];

    function animate() {
      bar.pos += bar.speed;
      bar.el.style.left = bar.pos + 'px';

      if (bar.pos < bar.stop) {
        requestAnimationFrame(animate);
      } else {
        bar.el.style.left = bar.stop + 'px';

        // 次のバーを遅れてスライド（500ms後）
        setTimeout(() => slideBar(index + 1), 500);
      }
    }

    animate();
  }

  window.addEventListener("DOMContentLoaded", () => {
    slideBar(0); // 最初のバーから開始
  });

window.addEventListener("DOMContentLoaded", slideInTitle);



