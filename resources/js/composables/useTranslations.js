import translations from '../translations.json';

export function useTranslations() {
  const getLocale = () => {
    return document.documentElement.lang || 'de';
  };

  const trans = (key, replacements = {}) => {
    const locale = getLocale();

    // If locale is German or translation doesn't exist, use the key (German text)
    let text = (locale === 'de' || !translations[key]) ? key : translations[key];

    // Replace placeholders like :year with actual values
    Object.keys(replacements).forEach(placeholder => {
      text = text.replace(new RegExp(`:${placeholder}`, 'g'), replacements[placeholder]);
    });

    return text;
  };

  return {
    trans,
    getLocale
  };
}
