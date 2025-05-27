import { ChangeOwner } from "@/components/change-owner"
import { TeamProfile } from "@/components/team-profile"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"

export default function TeamSettingsGeneral() {
  return (
    <TeamSettingsLayout>
      <Head title="General" />
      <TeamProfile />
      <ChangeOwner />
    </TeamSettingsLayout>
  )
}
