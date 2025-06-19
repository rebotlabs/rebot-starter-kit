import { useLaravelReactI18n } from "laravel-react-i18n"

// Custom hook for translations that uses laravel-react-i18n
export function useTranslations() {
  const { t: __, tChoice, currentLocale, setLocale } = useLaravelReactI18n()

  return { __, tChoice, currentLocale, setLocale }
}

// For backward compatibility, also export the hook function directly
export const useLaravelTranslations = useLaravelReactI18n
