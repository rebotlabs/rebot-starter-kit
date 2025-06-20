import { ChangeOwner } from "@/components/change-owner"
import { DeleteOrganization } from "@/components/delete-organization"
import { OrganizationProfile } from "@/components/organization-profile"
import { LogoUpload } from "@/components/organization/logo-upload"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import { Head } from "@inertiajs/react"

export default function OrganizationSettingsGeneral() {
  return (
    <OrganizationSettingsLayout>
      <Head title="General" />
      <div className="space-y-6">
        <LogoUpload />
        <OrganizationProfile />
        <ChangeOwner />
        <DeleteOrganization />
      </div>
    </OrganizationSettingsLayout>
  )
}
