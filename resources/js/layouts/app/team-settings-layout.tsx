import Heading from "@/components/heading"
import { Button } from "@/components/ui/button"
import { Separator } from "@/components/ui/separator"
import { TeamLayout } from "@/layouts/app/team-layout"
import { cn } from "@/lib/utils"
import type { NavItem, SharedData } from "@/types"
import { Link, usePage } from "@inertiajs/react"
import type { PropsWithChildren } from "react"

export const TeamSettingsLayout = ({ children }: PropsWithChildren) => {
  const page = usePage<SharedData>()
  const { currentTeam } = page.props

  const navigation: NavItem[] = [
    {
      title: "General",
      href: route("team.settings", [currentTeam], false),
      isActive: route().current() === "team.settings",
    },
    {
      title: "Members",
      href: route("team.settings.members", [currentTeam], false),
      isActive: route().current() === "team.settings.members",
    },
    {
      title: "Billing",
      href: route("team.settings.billing", [currentTeam], false),
      isActive: route().current() === "team.settings.billing",
    },
  ]

  return (
    <TeamLayout>
      <div className="px-4 py-6">
        <Heading title="Settings" description="Manage your profile and account settings" />

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
                    "bg-muted": item.isActive,
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
    </TeamLayout>
  )
}
