import { TeamProfile } from "@/components/team-profile"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import type { Team } from "@/types"
import { Head } from "@inertiajs/react"

export default function TeamSettingsGeneral({ team }: { team: Team }) {
  return (
    <TeamSettingsLayout>
      <Head title="General" />
      <TeamProfile />
    </TeamSettingsLayout>
  )
}
