import { ChangeOwner } from "@/components/change-owner"
import { OrganizationProfile } from "@/components/organization-profile"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import { Head } from "@inertiajs/react"

export default function OrganizationSettingsGeneral() {
  return (
    <OrganizationSettingsLayout>
      <Head title="General" />
      <OrganizationProfile />
      <ChangeOwner />
    </OrganizationSettingsLayout>
  )
}
