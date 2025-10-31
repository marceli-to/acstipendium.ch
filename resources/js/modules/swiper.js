// Swiper module
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// Store swiper instances
const swiperInstances = new WeakMap();

/**
 * Update caption text for swiper
 */
const updateCaption = (swiper, captionEl) => {
  if (!captionEl) return;

  const activeSlide = swiper.slides[swiper.activeIndex];
  const img = activeSlide?.querySelector('img');

  if (img && img.alt) {
    captionEl.textContent = img.alt;
  } else {
    captionEl.textContent = '';
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

  const showCaptionWithAutoHide = () => {
    captionEl.classList.remove('hidden');
    infoBtn.classList.add('hidden');

    // Clear existing timeout
    if (hideTimeout) {
      clearTimeout(hideTimeout);
    }

    // Hide caption after 2 seconds
    hideTimeout = setTimeout(() => {
      captionEl.classList.add('hidden');
      infoBtn.classList.remove('hidden');
    }, 3000);
  };

  const showCaptionPermanent = () => {
    captionEl.classList.remove('hidden');
    infoBtn.classList.add('hidden');

    // Clear any existing timeout
    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }
  };

  const hideCaption = () => {
    if (hideTimeout) {
      clearTimeout(hideTimeout);
    }
    captionEl.classList.add('hidden');
    infoBtn.classList.remove('hidden');
  };

  // Info button click shows caption permanently (no auto-hide)
  infoBtn.addEventListener('click', showCaptionPermanent);

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

  // Find navigation buttons in parent container
  const container = element.parentElement;
  const captionEl = container?.querySelector('.swiper-caption');

  // Setup caption toggle behavior
  const captionToggle = setupCaptionToggle(container);

  const swiper = new Swiper(element, {
    modules: [Navigation],
    slidesPerView: 1,
    width: element.offsetWidth, // Explicitly set width to prevent miscalculation
    spaceBetween: 16,
    loop: true, // Enable carousel/loop mode

    // Navigation arrows - scoped to this swiper instance
    navigation: {
      nextEl: container?.querySelector('.swiper-nav-next'),
      prevEl: container?.querySelector('.swiper-nav-prev'),
    },

    on: {
      init: function() {
        updateCaption(this, captionEl);
        // Show caption initially with auto-hide after 2 seconds
        if (captionToggle) {
          captionToggle.showCaptionWithAutoHide();
        }
      },
      slideChange: function() {
        updateCaption(this, captionEl);
        // Show caption when slide changes with auto-hide after 2 seconds
        if (captionToggle) {
          captionToggle.showCaptionWithAutoHide();
        }
      }
    }
  });

  swiperInstances.set(element, swiper);
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

// Export for manual initialization if needed
export default initSwiper;
