import { ref, watch } from 'vue';

// Initialize from localStorage or default to 'dark'
const currentTheme = ref(
  (typeof localStorage !== 'undefined' && localStorage.getItem('nexus_theme')) || 'dark'
);

function applyTheme(theme) {
  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('data-theme', theme);
    // Also set/remove the class for potential CSS selectors
    document.documentElement.classList.toggle('light', theme === 'light');
    document.documentElement.classList.toggle('dark', theme === 'dark');
  }
}

function setTheme(theme) {
  if (['dark', 'light'].includes(theme)) {
    currentTheme.value = theme;
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('nexus_theme', theme);
    }
    applyTheme(theme);
  }
}

function toggleTheme() {
  setTheme(currentTheme.value === 'dark' ? 'light' : 'dark');
}

// Apply theme immediately on import
applyTheme(currentTheme.value);

export function useTheme() {
  return {
    theme: currentTheme,
    setTheme,
    toggleTheme,
    isDark: ref(currentTheme.value === 'dark'),
  };
}
