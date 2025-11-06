// Swiper module
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// Store swiper instances
const swiperInstances = new WeakMap();
// Keep track of all swiper elements for resize handling
const allSwiperElements = new Set();

/**
 * Update caption text for swiper
 */
const updateCaption = (swiper, captionEl) => {
  if (!captionEl) return false;

  const activeSlide = swiper.slides[swiper.activeIndex];
  const img = activeSlide?.querySelector('img');

  if (img && img.alt) {
    captionEl.textContent = img.alt;
    return true; // Has caption
  } else {
    captionEl.textContent = '';
    return false; // No caption
  }
};

/**
 * Setup caption/info button toggle behavior
 */
const setupCaptionToggle = (container) => {
  const captionEl = container?.querySelector('.swiper-caption');
  const infoBtn = container?.querySelector('.swiper-info-btn');

  if (!captionEl || !infoBtn) return;

  let hideTimeout = null;

  // Make caption clickable
  captionEl.style.cursor = 'pointer';

  const showCaptionWithAutoHide = (hasCaption) => {
    // Only show if there's actually a caption
    if (!hasCaption) {
      captionEl.classList.add('opacity-0');
      captionEl.style.pointerEvents = 'none';
      infoBtn.classList.add('opacity-0');
      infoBtn.style.pointerEvents = 'none';
      return;
    }

    captionEl.classList.remove('opacity-0');
    captionEl.style.pointerEvents = 'auto';
    infoBtn.classList.add('opacity-0');
    infoBtn.style.pointerEvents = 'none';

    // Clear existing timeout
    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }

    // Hide caption after 3 seconds
    hideTimeout = setTimeout(() => {
      captionEl.classList.add('opacity-0');
      captionEl.style.pointerEvents = 'none';
      infoBtn.classList.remove('opacity-0');
      infoBtn.style.pointerEvents = 'auto';
      hideTimeout = null;
    }, 3000);
  };

  const showCaptionPermanent = () => {
    captionEl.classList.remove('opacity-0');
    captionEl.style.pointerEvents = 'auto';
    infoBtn.classList.add('opacity-0');
    infoBtn.style.pointerEvents = 'none';

    // Clear any existing timeout
    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }
  };

  const hideCaption = () => {
    // Clear any existing timeout
    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }
    captionEl.classList.add('opacity-0');
    captionEl.style.pointerEvents = 'none';
    infoBtn.classList.remove('opacity-0');
    infoBtn.style.pointerEvents = 'auto';
  };

  // Info button click shows caption permanently (no auto-hide)
  infoBtn.addEventListener('click', showCaptionPermanent);

  // Caption click hides it
  captionEl.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent click from reaching the info button below
    hideCaption();
  });

  return { showCaptionWithAutoHide, showCaptionPermanent, hideCaption };
};

/**
 * Initialize a single swiper
 */
const initSwiper = (element) => {
  // Skip if already initialized
  if (swiperInstances.has(element)) {
    return swiperInstances.get(element);
  }

  // Find navigation buttons - they're siblings to the swiper element
  // The browse partial is rendered after the swiper div
  const container = element.parentElement;
  const browseEl = element.nextElementSibling;
  const captionEl = browseEl?.querySelector('.swiper-caption');
  const navPrev = browseEl?.querySelector('.swiper-nav-prev');
  const navNext = browseEl?.querySelector('.swiper-nav-next');

  // Setup caption toggle behavior
  const captionToggle = setupCaptionToggle(browseEl);

  const swiper = new Swiper(element, {
    modules: [Navigation],
    slidesPerView: 1,
    width: element.offsetWidth, // Explicitly set width to prevent miscalculation
    spaceBetween: 16,
    loop: true, // Enable carousel/loop mode

    // Navigation arrows - scoped to this swiper instance
    navigation: {
      nextEl: navNext,
      prevEl: navPrev,
    },

    on: {
      init: function() {
        const hasCaption = updateCaption(this, captionEl);
        // Show caption initially with auto-hide after 2 seconds
        if (captionToggle) {
          captionToggle.showCaptionWithAutoHide(hasCaption);
        }
      },
      slideChange: function() {
        const hasCaption = updateCaption(this, captionEl);
        // Show caption when slide changes with auto-hide after 2 seconds
        if (captionToggle) {
          captionToggle.showCaptionWithAutoHide(hasCaption);
        }
      }
    }
  });

  swiperInstances.set(element, swiper);
  allSwiperElements.add(element);
  return swiper;
};

// Listen for accordion open events and initialize swipers
document.addEventListener('accordion-opened', (event) => {
  const container = event.detail;

  const initAll = () => {
    const swipers = container.querySelectorAll('[data-swiper]');
    swipers.forEach((el) => {
      initSwiper(el);
    });
  };

  let initialized = false;

  const onEnd = (e) => {
    if (e.propertyName === 'max-height' && !initialized) {
      initialized = true;
      initAll();
      container.removeEventListener('transitionend', onEnd);
    }
  };

  container.addEventListener('transitionend', onEnd);

  // Fallback if no transition
  const cs = getComputedStyle(container);
  const hasMaxHeightTransition =
    cs.transitionProperty.includes('max-height') || cs.transitionProperty.trim() === 'all';
  const maxDur = Math.max(...cs.transitionDuration.split(',').map(s => parseFloat(s) || 0));

  if (!hasMaxHeightTransition || maxDur === 0) {
    requestAnimationFrame(() => { if (!initialized) initAll(); });
  } else {
    const safety = Math.max(500, (maxDur * 1000) + 50);
    setTimeout(() => { if (!initialized) initAll(); }, safety);
  }
});

// Handle window resize - update all active swiper instances
let resizeTimeout;
window.addEventListener('resize', () => {
  // Debounce resize events
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    allSwiperElements.forEach((element) => {
      const swiper = swiperInstances.get(element);
      if (swiper && !swiper.destroyed) {
        // Update the width parameter and force recalculation
        swiper.params.width = element.offsetWidth;
        swiper.update();
      }
    });
  }, 50);
});

// Export for manual initialization if needed
export default initSwiper;
