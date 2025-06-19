import PasswordManagement from "@/components/settings/password-management"
import TwoFactorAuthentication from "@/components/settings/two-factor-authentication"
import { useLang } from "@/hooks/useLang"
import AppLayout from "@/layouts/app-layout"
import SettingsLayout from "@/layouts/settings/layout"
import { Head } from "@inertiajs/react"

interface Props {
  twoFactorEnabled: boolean
  recoveryCodes?: string[]
}

export default function Security({ twoFactorEnabled, recoveryCodes }: Props) {
  const { __ } = useLang()

  return (
    <AppLayout navigation={[]}>
      <Head title={__("settings.security.title")} />

      <SettingsLayout>
        <div className="space-y-12">
          <PasswordManagement />
          <TwoFactorAuthentication twoFactorEnabled={twoFactorEnabled} recoveryCodes={recoveryCodes} />
        </div>
      </SettingsLayout>
    </AppLayout>
  )
}
