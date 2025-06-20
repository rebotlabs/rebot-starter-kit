import { useTranslation } from "@/hooks/use-i18n"
import AppLayout from "@/layouts/app-layout"
import type { NavItem, SharedData } from "@/types"
import { usePage } from "@inertiajs/react"
import type { PropsWithChildren } from "react"

export const OrganizationLayout = ({ children }: PropsWithChildren) => {
  const t = useTranslation()
  const page = usePage<SharedData>()
  const { currentOrganization } = page.props

  const navigation: NavItem[] = [
    {
      title: t("nav.overview"),
      href: route("organization.overview", [currentOrganization], false),
      isActive: route().current() === "organization.overview",
    },
    {
      title: t("nav.settings"),
      href: route("organization.settings", [currentOrganization], false),
      isActive: route().current()?.startsWith("organization.settings"),
    },
  ]

  return <AppLayout navigation={navigation}>{children}</AppLayout>
}
