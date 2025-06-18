import { ChangeOwner } from "@/components/change-owner"
import { DeleteOrganization } from "@/components/delete-organization"
import { OrganizationProfile } from "@/components/organization-profile"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import { Head } from "@inertiajs/react"

export default function OrganizationSettingsGeneral() {
  return (
    <OrganizationSettingsLayout>
      <Head title="General" />
      <OrganizationProfile />
      <ChangeOwner />
      <DeleteOrganization />
    </OrganizationSettingsLayout>
  )
}
