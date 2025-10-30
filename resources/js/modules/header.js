document.addEventListener('alpine:init', () => {
  window.Alpine.data('header', () => ({
    menu: false,
    hidden: false,
    lastScrollY: 0,
    scrollUpStartY: 0,
    ticking: false,
    ignoreScroll: false,
    showThreshold: 25, // pixels to scroll up before showing header

    init() {
      this.lastScrollY = window.scrollY;
      this.scrollUpStartY = window.scrollY;

      window.addEventListener('scroll', () => {
        if (!this.ticking) {
          window.requestAnimationFrame(() => this.updateHeaderVisibility());
          this.ticking = true;
        }
      });

      // Listen for accordion scroll events
      window.addEventListener('accordion-scrolling', () => {
        this.ignoreScroll = true;
        // Re-enable after scroll animation completes (1 second to be safe)
        setTimeout(() => {
          this.ignoreScroll = false;
        }, 500);
      });
    },

    updateHeaderVisibility() {
      const currentScrollY = window.scrollY;

      // Ignore programmatic scrolls (e.g., from accordion)
      if (this.ignoreScroll) {
        this.lastScrollY = currentScrollY;
        this.scrollUpStartY = currentScrollY; // Keep this in sync
        this.ticking = false;
        return;
      }

      // Ignore small scroll differences
      if (Math.abs(currentScrollY - this.lastScrollY) < 10) {
        this.ticking = false;
        return;
      }

      // Scrolling down
      if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
        this.hidden = true;
        this.menu = false; // close menu when header hides
        this.scrollUpStartY = currentScrollY; // Track where we start measuring from
      }
      // Scrolling up
      else if (currentScrollY < this.lastScrollY) {
        // If just started scrolling up, mark the starting position
        if (this.lastScrollY > currentScrollY && currentScrollY < this.scrollUpStartY) {
          // Check if we've scrolled up enough to show the header
          if (this.hidden && (this.scrollUpStartY - currentScrollY) >= this.showThreshold) {
            this.hidden = false;
          }
        } else if (!this.hidden) {
          // Keep it visible if already visible
          this.hidden = false;
        }
      }

      this.lastScrollY = currentScrollY;
      this.ticking = false;
    },
  }));
});
