import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import { Head } from "@inertiajs/react"

export default function OrganizationSettingsBilling() {
  return (
    <OrganizationSettingsLayout>
      <Head title="Billing" />
      <Card>
        <CardHeader>
          <CardTitle>Billing information</CardTitle>
          <CardDescription>Update your billing information</CardDescription>
        </CardHeader>
      </Card>
    </OrganizationSettingsLayout>
  )
}
