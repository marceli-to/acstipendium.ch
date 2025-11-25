// Accordion module for Alpine.js

// Individual Item Component
export const AccordionItem = (index) => ({
  get open() {
    return this.selected === index;
  },

  updateHeight() {
    if (this.open) {
      const container = this.$refs.container;
      container.style.maxHeight = container.scrollHeight + 'px';
    }
  },

  init() {
    this.$watch('selected', async () => {
      if (this.open) {
        await this.$nextTick(); // wait for DOM update

        // Check if still open after wait to avoid race condition
        if (!this.open) return;

        // Mark this accordion as open for resize handler
        this.$el.dataset.accordionOpen = 'true';

        // now container is real and measurable
        this.updateHeight();

        // dispatch event safely on document
        document.dispatchEvent(new CustomEvent('accordion-opened', { detail: this.$refs.container }));

        // Scroll the accordion item into view
        this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      else {
        delete this.$el.dataset.accordionOpen;
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

// Group/Wrapper Component
export const AccordionGroup = () => ({
  selected: null,

  init() {
    // Handle hash-based opening scoped to this group
    // Defer hash check until after Alpine has fully initialized all children
    if (window.location.hash) {
      this.$nextTick(() => {
        this.checkHash();
      });
    }
  },

  checkHash() {
    const hash = window.location.hash.substring(1);
    if (!hash) return;
    // Escape the hash to prevent selector errors
    const safeHash = CSS.escape(hash);
    
    // Search only within this group
    const targetElement = this.$el.querySelector(`[data-slug="${safeHash}"]`);

    if (targetElement && targetElement.hasAttribute('x-data')) {
      // Get the index from the Alpine component or infer it
      // Since we are in the group, we need to find which child index this element corresponds to.
      // However, AccordionItem(index) sets the index.
      // We can find the index by getting all items and finding the index of the match.
      const allItems = Array.from(this.$el.querySelectorAll('[x-data*="AccordionItem"]'));
      const targetIndex = allItems.findIndex(item => item === targetElement);

      if (targetIndex !== -1) {
        this.selected = targetIndex;
      }
    }
  }
});

// Register on window for Alpine
window.AccordionItem = AccordionItem;
window.AccordionGroup = AccordionGroup;

// Handle window resize - update all open accordion heights globally
// This is kept global for performance (one listener instead of N)
let resizeTimeout;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    // Only find open accordion items
    const openAccordionItems = document.querySelectorAll('[data-accordion-open]');

    openAccordionItems.forEach((element) => {
      // Get the Alpine component and update height
      const Alpine = window.Alpine;
      if (!Alpine) return;

      const component = Alpine.$data(element);
      if (component && component.updateHeight) {
        component.updateHeight();
      }
    });
  }, 100);
});
