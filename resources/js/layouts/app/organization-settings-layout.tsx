import Heading from "@/components/heading"
import { Button } from "@/components/ui/button"
import { Separator } from "@/components/ui/separator"
import { useTranslation } from "@/hooks/use-i18n"
import { OrganizationLayout } from "@/layouts/app/organization-layout"
import { cn } from "@/lib/utils"
import type { NavItem, SharedData } from "@/types"
import { Link, usePage } from "@inertiajs/react"
import type { PropsWithChildren } from "react"

export const OrganizationSettingsLayout = ({ children }: PropsWithChildren) => {
  const t = useTranslation()
  const page = usePage<SharedData>()
  const { currentOrganization, currentUserCanManage } = page.props

  const navigation: NavItem[] = currentUserCanManage
    ? [
        {
          title: t("nav.general"),
          href: route("organization.settings", [currentOrganization], false),
          isActive: route().current() === "organization.settings",
        },
        {
          title: t("nav.members"),
          href: route("organization.settings.members", [currentOrganization], false),
          isActive: route().current() === "organization.settings.members",
        },
        {
          title: t("nav.billing"),
          href: route("organization.settings.billing", [currentOrganization], false),
          isActive: route().current() === "organization.settings.billing",
        },
      ]
    : [
        {
          title: t("nav.general"),
          href: route("organization.settings.leave", [currentOrganization], false),
          isActive: route().current() === "organization.settings.leave",
        },
      ]

  return (
    <OrganizationLayout>
      <div className="px-4 py-6">
        <Heading title={t("settings.title")} description={t("settings.description")} />

        <div className="flex flex-col space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12">
          <aside className="w-full max-w-xl lg:w-48">
            <nav className="flex flex-col space-y-1 space-x-0">
              {navigation.map((item, index) => (
                <Button
                  key={`${item.href}-${index}`}
                  size="sm"
                  variant="ghost"
                  asChild
                  className={cn("w-full justify-start", {
                    "bg-muted font-bold": item.isActive,
                  })}
                >
                  <Link href={item.href} prefetch>
                    {item.title}
                  </Link>
                </Button>
              ))}
            </nav>
          </aside>

          <Separator className="my-6 md:hidden" />

          <div className="flex-1 md:max-w-2xl">
            <section className="max-w-xl space-y-12">{children}</section>
          </div>
        </div>
      </div>
    </OrganizationLayout>
  )
}
