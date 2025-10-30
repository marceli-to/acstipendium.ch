// Accordion module for Alpine.js
export const AccordionItem = (index) => ({
  
  get open() {
    return this.selected === index;
  },

  init() {
    // Watch for changes to selected
    this.$watch('selected', () => {
      if (this.open) {
        setTimeout(() => {
          this.$refs.container.style.maxHeight = this.$refs.container.scrollHeight + 'px';
          // Scroll the accordion item into view
          this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 10);
      } else {
        this.$refs.container.style.maxHeight = '0px';
      }
    });
  },

  toggle() {
    // Tell header to ignore scroll BEFORE any changes happen
    window.dispatchEvent(new CustomEvent('accordion-scrolling'));

    if (this.selected === index) {
      // Close current item
      this.selected = null;
    } else {
      // Open this item (closes others automatically)
      this.selected = index;
    }
  }
});

// Make it globally accessible for Alpine.js
window.AccordionItem = AccordionItem;