import AppLayoutTemplate from "@/layouts/app/app-header-layout"
import type { NavItem } from "@/types"
import { type ReactNode } from "react"

interface AppLayoutProps {
  children: ReactNode
  navigation: NavItem[]
}

export default ({ children, ...props }: AppLayoutProps) => <AppLayoutTemplate {...props}>{children}</AppLayoutTemplate>
