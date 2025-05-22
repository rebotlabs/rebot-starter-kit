import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"
import HeadingSmall from "@/components/heading-small"

export default function TeamSettingsMembers() {
  return (
    <TeamSettingsLayout>
      <Head title="Members" />
      <div className="space-y-6">
        <HeadingSmall title="Members" description="Manage members of your team" />
      </div>
    </TeamSettingsLayout>
  )
}
