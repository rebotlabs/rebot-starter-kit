import { Head } from "@inertiajs/react"

import AppearanceTabs from "@/components/appearance-tabs"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import AppLayout from "@/layouts/app-layout"
import SettingsLayout from "@/layouts/settings/layout"

export default function Appearance() {
  return (
    <AppLayout navigation={[]}>
      <Head title="Appearance settings" />

      <SettingsLayout>
        <Card>
          <CardHeader>
            <CardTitle>Appearance settings</CardTitle>
            <CardDescription>Update your account's appearance settings</CardDescription>
          </CardHeader>
          <CardContent>
            <AppearanceTabs />
          </CardContent>
        </Card>
      </SettingsLayout>
    </AppLayout>
  )
}
