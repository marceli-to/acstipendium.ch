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
      } else {
        this.$refs.container.style.maxHeight = '0px';
      }
    });
  },

  toggle() {
    this.selected = this.selected === index ? null : index;
  }
});

window.AccordionItem = AccordionItem;
