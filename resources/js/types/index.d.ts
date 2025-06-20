import { LucideIcon } from "lucide-react"
import type { Config } from "ziggy-js"

export interface Auth {
  user: User
}

export interface BreadcrumbItem {
  title: string
  href: string
}

export interface NavGroup {
  title: string
  items: NavItem[]
}

export interface NavItem {
  title: string
  href: string
  icon?: LucideIcon | null
  isActive?: boolean
}

export interface SharedData {
  name: string
  quote: { message: string; author: string }
  auth: Auth
  ziggy: Config & { location: string }
  sidebarOpen: boolean
  currentOrganization: Organization
  organizations: Organization[]
  currentUserRole: string | null
  currentUserCanManage: boolean

  [key: string]: unknown
}

export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  email_verified_at: string | null
  created_at: string
  updated_at: string

  [key: string]: unknown // This allows for additional properties...
}

export interface Notification {
  id: string
  type: string
  data: Record<string, unknown>
  read_at: string | null
  created_at: string
}

export interface NotificationData {
  notifications: Notification[]
  unread_count: number
}

export interface Organization {
  id: number
  name: string
  slug: string
  logo?: string
  owner_id: number
  created_at: string
  updated_at: string

  [key: string]: unknown
}

export interface Invitation {
  id: number
  email: string
  organization_id: number
  user_id: number | null
  user: User | null
  role: string
  status: string
  accept_token: string
  reject_token: string
  created_at: string
  updated_at: string

  [key: string]: unknown
}

export interface Member {
  id: number
  user: User
  role: string
  created_at: string
  updated_at: string

  [key: string]: unknown
}
