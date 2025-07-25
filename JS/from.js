const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => {
  sidebar.classList.add('show');
  hamburger.style.display = 'none';
  closeBtn.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
  sidebar.classList.remove('show');
  hamburger.style.display = 'flex';
  closeBtn.style.display = 'none';
});
