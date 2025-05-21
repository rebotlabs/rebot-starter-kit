import { AppContent } from "@/components/app-content"
import { AppHeader } from "@/components/app-header"
import { AppShell } from "@/components/app-shell"
import type { NavItem } from "@/types"
import type { PropsWithChildren } from "react"

export default function AppHeaderLayout({ children, navigation }: PropsWithChildren<{ navigation: NavItem[] }>) {
  return (
    <AppShell>
      <AppHeader navigation={navigation} />
      <AppContent>{children}</AppContent>
    </AppShell>
  )
}
