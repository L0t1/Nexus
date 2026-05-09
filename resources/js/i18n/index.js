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
 * Falls back to English if the key is not found in the current locale.
 * Falls back to the key itself if not found in any locale.
 */
function t(key) {
  const keys = key.split('.');
  let val = locales[currentLocale.value];
  for (const k of keys) {
    if (val && typeof val === 'object' && k in val) {
      val = val[k];
    } else {
      val = undefined;
      break;
    }
  }

  // Fallback to English
  if (val === undefined && currentLocale.value !== 'en') {
    val = locales.en;
    for (const k of keys) {
      if (val && typeof val === 'object' && k in val) {
        val = val[k];
      } else {
        val = undefined;
        break;
      }
    }
  }

  return val ?? key;
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
    locale: currentLocale,
    setLocale,
    localeNames,
    getAvailableLocales,
  };
}
