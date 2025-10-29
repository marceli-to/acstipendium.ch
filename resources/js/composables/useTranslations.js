import translations from '../translations.json';

export function useTranslations() {
  const getLocale = () => {
    return document.documentElement.lang || 'de';
  };

  const trans = (key) => {
    const locale = getLocale();

    // If locale is German or translation doesn't exist, return the key (German text)
    if (locale === 'de' || !translations[key]) {
      return key;
    }

    // Return the French translation
    return translations[key];
  };

  return {
    trans,
    getLocale
  };
}
