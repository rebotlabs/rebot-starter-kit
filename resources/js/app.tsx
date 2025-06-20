import "../css/app.css"

import { createInertiaApp } from "@inertiajs/react"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import { createRoot } from "react-dom/client"
import { initializeTheme } from "./hooks/use-appearance"
import { I18nProvider } from "./hooks/use-i18n"

const appName = import.meta.env.VITE_APP_NAME || "Laravel"

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob("./pages/**/*.tsx")),
  setup({ el, App, props }) {
    const root = createRoot(el)

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const i18nProps = (props.initialPage.props as any).i18n || { current: "en", default: "en", messages: {} }

    // Create initial messages object with the SSR messages to prevent flickering
    const initialMessages = {
      [i18nProps.current]: i18nProps.messages || {},
      [i18nProps.default]: i18nProps.messages || {},
    }

    root.render(
      <I18nProvider initialLocale={i18nProps.current} fallbackLocale={i18nProps.default} initialMessages={initialMessages}>
        <App {...props} />
      </I18nProvider>,
    )
  },
  progress: {
    color: "#4B5563",
  },
})

// This will set light / dark mode on load...
initializeTheme()
