import { Head } from "@inertiajs/react"

import DeleteUser from "@/components/delete-user"
import { ProfileForm } from "@/components/settings/profile-form"
import { useLang } from "@/hooks/useLang"
import AppLayout from "@/layouts/app-layout"
import SettingsLayout from "@/layouts/settings/layout"

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
  const { __ } = useLang()

  return (
    <AppLayout navigation={[]}>
      <Head title={__("settings.profile.title")} />

      <SettingsLayout>
        <ProfileForm mustVerifyEmail={mustVerifyEmail} status={status} />
        <DeleteUser />
      </SettingsLayout>
    </AppLayout>
  )
}
