import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import { Head } from "@inertiajs/react"
import HeadingSmall from "@/components/heading-small"

export default function TeamSettingsBilling() {
  return (
    <TeamSettingsLayout>
      <Head title={"Billing"} />
      <div className="space-y-6">
        <HeadingSmall title="Billing informations" description="Update your billing informations" />
      </div>
    </TeamSettingsLayout>
  )
}
