/**
 * Exhibitions sorting Alpine.js component
 */
export default function exhibitionsSort() {
  return {
    get sortedItems() {
      const items = Array.from(this.$el.querySelectorAll('[data-year]'));

      // Get sortDirection from parent scope (body element)
      const sortDirection = this.$root.sortDirection;

      return items.sort((a, b) => {
        const yearA = parseInt(a.dataset.year);
        const yearB = parseInt(b.dataset.year);
        return sortDirection === 'asc' ? yearA - yearB : yearB - yearA;
      });
    },

    init() {
      this.$effect(() => {
        this.sortedItems.forEach((item, index) => {
          item.style.order = index;
        });
      });
    }
  }
}
