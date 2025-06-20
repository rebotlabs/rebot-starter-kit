import { createInertiaApp } from "@inertiajs/react"
import createServer from "@inertiajs/react/server"
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers"
import ReactDOMServer from "react-dom/server"
import { type RouteName, route } from "ziggy-js"
import { I18nProvider } from "./hooks/use-i18n"

const appName = import.meta.env.VITE_APP_NAME || "Laravel"

createServer((page) =>
  createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob("./pages/**/*.tsx")),
    setup: ({ App, props }) => {
      /* eslint-disable */
      // @ts-expect-error
      global.route<RouteName> = (name, params, absolute) =>
        route(name, params as any, absolute, {
          // @ts-expect-error
          ...page.props.ziggy,
          // @ts-expect-error
          location: new URL(page.props.ziggy.location),
        })
      /* eslint-enable */

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const i18nProps = (props.initialPage.props as any).i18n || { current: "en", default: "en", messages: {} }

      // Create initial messages object with the SSR messages to prevent flickering
      const initialMessages = {
        [i18nProps.current]: i18nProps.messages || {},
        [i18nProps.default]: i18nProps.messages || {},
      }

      return (
        <I18nProvider initialLocale={i18nProps.current} fallbackLocale={i18nProps.default} initialMessages={initialMessages}>
          <App {...props} />
        </I18nProvider>
      )
    },
  }),
)
