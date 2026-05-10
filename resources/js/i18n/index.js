import en from './en.js';
import sq from './sq.js';
import { ref, computed } from 'vue';

const locales = { en, sq };
const localeNames = { en: 'English', sq: 'Shqip' };

// Initialize from localStorage or default to 'en'
const currentLocale = ref(
  (typeof localStorage !== 'undefined' && localStorage.getItem('nexus_locale')) || 'en'
);

/**
 * Deep-get a nested translation key using dot notation.
 * Supports variable replacement: t('key', { name: 'Sara' })
 */
function t(key, params = {}, fallback = null) {
  const [section, ...rest] = key.split('.');
  const subKey = rest.join('.');
  
  let val = locales[currentLocale.value]?.[section]?.[subKey];

  // Fallback to English
  if (val === undefined && currentLocale.value !== 'en') {
    val = locales.en?.[section]?.[subKey];
  }

  if (val === undefined) return fallback ?? key;

  // Simple string interpolation for {variable}
  if (typeof val === 'string') {
    let replaced = val;
    Object.keys(params).forEach(p => {
      replaced = replaced.replace(new RegExp(`{${p}}`, 'g'), params[p]);
    });
    
    // If we still have {brackets} in the string, it means we missed some variables.
    // In this case, if we have a fallback description, use that instead of a broken template.
    if (fallback && /{[a-zA-Z0-9_]+}/.test(replaced)) {
      return fallback;
    }
    
    return replaced;
  }

  return val;
}

function te(key) {
  const [section, ...rest] = key.split('.');
  const subKey = rest.join('.');
  
  const val = locales[currentLocale.value]?.[section]?.[subKey];
  if (val !== undefined) return true;

  if (currentLocale.value !== 'en') {
    return locales.en?.[section]?.[subKey] !== undefined;
  }

  return false;
}

function setLocale(locale) {
  if (locales[locale]) {
    currentLocale.value = locale;
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('nexus_locale', locale);
    }
    // Update the HTML lang attribute
    if (typeof document !== 'undefined') {
      document.documentElement.lang = locale;
    }
  }
}

function getAvailableLocales() {
  return Object.entries(localeNames).map(([code, name]) => ({ code, name }));
}

export function useI18n() {
  return {
    t,
    te,
    locale: currentLocale,
    setLocale,
    localeNames,
    getAvailableLocales,
  };
}
