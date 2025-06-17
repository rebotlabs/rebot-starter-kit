import { Invitations } from "@/components/invitations"
import { InviteUser } from "@/components/invite-user"
import { MembersList } from "@/components/members-list"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { OrganizationSettingsLayout } from "@/layouts/app/organization-settings-layout"
import { Head } from "@inertiajs/react"

export default function OrganizationSettingsMembers() {
  return (
    <OrganizationSettingsLayout>
      <Head title="Members" />
      <Card>
        <CardHeader>
          <CardTitle>Members</CardTitle>
          <CardDescription>Manage members of your organization</CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <InviteUser />
          <Invitations />
          <MembersList />
        </CardContent>
      </Card>
    </OrganizationSettingsLayout>
  )
}
