import AppLayout from "@/layouts/app-layout"
import type { NavItem, SharedData } from "@/types"
import { usePage } from "@inertiajs/react"
import type { PropsWithChildren } from "react"

export const TeamLayout = ({ children }: PropsWithChildren) => {
  const { currentTeam } = usePage<SharedData>().props

  const navigation: NavItem[] = [
    {
      title: "Overview",
      href: route("team.overview", [currentTeam], false),
    },
    {
      title: "Settings",
      href: "#",
    },
  ]

  return <AppLayout navigation={navigation}>{children}</AppLayout>
}
