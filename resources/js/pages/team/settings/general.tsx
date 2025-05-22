import HeadingSmall from "@/components/heading-small"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"

export default function TeamSettingsGeneral() {
  return (
    <TeamSettingsLayout>
      <Head title="General" />
      <div className="space-y-6">
        <HeadingSmall title="General informations" description="Update your team informations" />
      </div>
    </TeamSettingsLayout>
  )
}
