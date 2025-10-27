document.addEventListener('alpine:init', () => {
  window.Alpine.data('header', () => ({
    menu: false,
    hidden: false,
    lastScrollY: 0,
    ticking: false,

    init() {
      this.lastScrollY = window.scrollY;

      window.addEventListener('scroll', () => {
        if (!this.ticking) {
          window.requestAnimationFrame(() => this.updateHeaderVisibility());
          this.ticking = true;
        }
      });
    },

    updateHeaderVisibility() {
      const currentScrollY = window.scrollY;

      // Ignore small scroll differences
      if (Math.abs(currentScrollY - this.lastScrollY) < 10) {
        this.ticking = false;
        return;
      }

      // Hide when scrolling down (past 100px)
      if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
        this.hidden = true;
        this.menu = false; // close menu when header hides
      } else {
        this.hidden = false;
      }

      this.lastScrollY = currentScrollY;
      this.ticking = false;
    },
  }));
});
