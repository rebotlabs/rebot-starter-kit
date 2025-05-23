import { InviteUser } from "@/components/invite-user"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"

export default function TeamSettingsMembers() {
  return (
    <TeamSettingsLayout>
      <Head title="Members" />
      <Card>
        <CardHeader>
          <CardTitle>Members</CardTitle>
          <CardDescription>Manage members of your team</CardDescription>
        </CardHeader>

        <CardContent>
          <InviteUser />
        </CardContent>
      </Card>
    </TeamSettingsLayout>
  )
}
