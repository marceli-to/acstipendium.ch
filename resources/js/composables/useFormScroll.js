export function useFormScroll(selector = 'form') {
  const scrollToForm = () => {
    const formElement = document.querySelector(selector);

    if (formElement) {
      formElement.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  };

  return {
    scrollToForm
  };
}
