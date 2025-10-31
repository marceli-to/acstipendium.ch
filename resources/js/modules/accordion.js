// Accordion module for Alpine.js
export const AccordionItem = (index) => ({
  get open() {
    return this.selected === index;
  },

  init() {
    this.$watch('selected', async () => {
      if (this.open) {
        await this.$nextTick(); // wait for DOM update

        // now container is real and measurable
        const container = this.$refs.container;
        container.style.maxHeight = container.scrollHeight + 'px';

        // dispatch event safely on document
        document.dispatchEvent(new CustomEvent('accordion-opened', { detail: container }));

        // Scroll the accordion item into view
        this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

window.AccordionItem = AccordionItem;

// Handle hash-based accordion opening on page load
document.addEventListener('alpine:initialized', () => {
  if (window.location.hash) {
    const hash = window.location.hash.substring(1);
    const targetElement = document.getElementById(hash);

    if (targetElement && targetElement.hasAttribute('x-data')) {
      // Find the parent accordion wrapper
      const wrapper = targetElement.closest('[x-data*="selected"]');

      if (wrapper) {
        // Get all accordion items within this wrapper
        const allItems = Array.from(wrapper.querySelectorAll('[x-data*="AccordionItem"]'));
        const targetIndex = allItems.findIndex(item => item === targetElement);

        if (targetIndex !== -1) {
          // Get the Alpine component and set selected
          const alpineComponent = Alpine.$data(wrapper);
          alpineComponent.selected = targetIndex;
        }
      }
    }
  }
});
