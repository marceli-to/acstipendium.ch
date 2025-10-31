/**
 * Fellows sorting Alpine.js component
 */
export default function fellowsSort() {
  return {
    sortFellows() {
      const items = Array.from(this.$el.querySelectorAll('[data-fellow]'));

      // Get sortBy and sortDirection from parent scope (body element)
      const sortBy = this.$root.sortBy;
      const sortDirection = this.$root.sortDirection;

      const sorted = items.sort((a, b) => {
        let valueA, valueB;

        switch(sortBy) {
          case 'name':
            valueA = a.dataset.name.toLowerCase();
            valueB = b.dataset.name.toLowerCase();
            return sortDirection === 'asc'
              ? valueA.localeCompare(valueB)
              : valueB.localeCompare(valueA);

          case 'scholarship':
            valueA = a.dataset.scholarship;
            valueB = b.dataset.scholarship;
            return sortDirection === 'asc'
              ? valueA.localeCompare(valueB)
              : valueB.localeCompare(valueA);

          case 'amount':
            valueA = parseInt(a.dataset.amount);
            valueB = parseInt(b.dataset.amount);
            return sortDirection === 'asc' ? valueA - valueB : valueB - valueA;

          case 'year':
          default:
            valueA = parseInt(a.dataset.year);
            valueB = parseInt(b.dataset.year);
            return sortDirection === 'asc' ? valueA - valueB : valueB - valueA;
        }
      });

      sorted.forEach((item, index) => {
        item.style.order = index;
      });
    },

    init() {
      // Watch parent scope variables
      this.$watch(() => this.$root.sortBy, () => this.sortFellows());
      this.$watch(() => this.$root.sortDirection, () => this.sortFellows());
      this.sortFellows();
    }
  }
}
