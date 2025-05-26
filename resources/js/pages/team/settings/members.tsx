import { Invitations } from "@/components/invitations"
import { InviteUser } from "@/components/invite-user"
import { MembersList } from "@/components/members-list"
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

        <CardContent className="space-y-6">
          <InviteUser />
          <Invitations />
          <MembersList />
        </CardContent>
      </Card>
    </TeamSettingsLayout>
  )
}
