// Accordion module for Alpine.js
export const AccordionItem = (index) => ({
  open: false,
  isTransitioning: false,

  init() {
    this.open = this.selected === index;
  },

  toggle() {
    if (this.open) {
      // Closing: change button and close content immediately (no transition)
      this.open = false;
      this.$refs.container.style.maxHeight = '0px';
    } else {
      // Opening: change button first, then open content
      this.open = true;

      setTimeout(() => {
        this.$refs.container.style.maxHeight = this.$refs.container.scrollHeight + 'px';
      }, 10);
    }
  }
});

// Make it globally accessible for Alpine.js
window.AccordionItem = AccordionItem;