import AppLayoutTemplate from "@/layouts/app/app-header-layout"
import { type ReactNode } from "react"
import type { NavItem } from "@/types"

interface AppLayoutProps {
  children: ReactNode
  navigation: NavItem[]
}

export default ({ children, ...props }: AppLayoutProps) => <AppLayoutTemplate {...props}>{children}</AppLayoutTemplate>
