import { Head } from "@inertiajs/react"

import DeleteUser from "@/components/delete-user"
import { AvatarUpload } from "@/components/settings/avatar-upload"
import { ProfileForm } from "@/components/settings/profile-form"
import { useTranslation } from "@/hooks/use-i18n"
import AppLayout from "@/layouts/app-layout"
import SettingsLayout from "@/layouts/settings/layout"

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
  const t = useTranslation()

  return (
    <AppLayout navigation={[]}>
      <Head title={t("settings.profile.title")} />

      <SettingsLayout>
        <div className="space-y-6">
          <AvatarUpload />
          <ProfileForm mustVerifyEmail={mustVerifyEmail} status={status} />
          <DeleteUser />
        </div>
      </SettingsLayout>
    </AppLayout>
  )
}
