import AppLayout from "@/layouts/app-layout"
import type { NavItem, SharedData } from "@/types"
import { usePage } from "@inertiajs/react"
import type { PropsWithChildren } from "react"

export const TeamLayout = ({ children }: PropsWithChildren) => {
  const page = usePage<SharedData>()
  const { currentTeam } = page.props

  const navigation: NavItem[] = [
    {
      title: "Overview",
      href: route("team.overview", [currentTeam], false),
      isActive: route().current() === "team.overview",
    },
    {
      title: "Settings",
      href: route("team.settings", [currentTeam], false),
      isActive: route().current()?.startsWith("team.settings"),
    },
  ]

  return <AppLayout navigation={navigation}>{children}</AppLayout>
}
