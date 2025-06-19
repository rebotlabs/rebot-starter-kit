import { LeaveOrganizationCard } from "@/components/organization/leave-organization-card"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import type { Member, Organization } from "@/types"
import { Head, usePage } from "@inertiajs/react"

interface LeaveOrganizationProps {
  organization: Organization
  member: Member
  [key: string]: unknown
}

export default function LeaveOrganization() {
  const { organization } = usePage<LeaveOrganizationProps>().props

  return (
    <OrganizationSettingsLayout>
      <Head title="Leave Organization" />

      <div className="space-y-6">
        <LeaveOrganizationCard organization={organization} />
      </div>
    </OrganizationSettingsLayout>
  )
}
