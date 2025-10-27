// --- Hide header on scroll down, show on scroll up ---
document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('[data-header]');
  if (!header) return;

  let lastScrollY = window.scrollY;
  let ticking = false;

  const updateHeaderVisibility = () => {
    const currentScrollY = window.scrollY;

    // Only trigger after slight movement (prevents flicker)
    if (Math.abs(currentScrollY - lastScrollY) < 10) {
      ticking = false;
      return;
    }

    if (currentScrollY > lastScrollY && currentScrollY > 100) {
      // scrolling down
      header.dataset.hidden = 'true';
    } else {
      // scrolling up
      header.dataset.hidden = 'false';
    }

    lastScrollY = currentScrollY;
    ticking = false;
  };

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(updateHeaderVisibility);
      ticking = true;
    }
  });
});
