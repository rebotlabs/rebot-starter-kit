import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"

export default function TeamSettingsBilling() {
  return (
    <TeamSettingsLayout>
      <Head title="Billing" />
      <Card>
        <CardHeader>
          <CardTitle>Billing information</CardTitle>
          <CardDescription>Update your billing information</CardDescription>
        </CardHeader>
      </Card>
    </TeamSettingsLayout>
  )
}
