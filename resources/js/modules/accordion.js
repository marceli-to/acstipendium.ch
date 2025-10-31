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
