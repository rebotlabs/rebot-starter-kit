import PasswordUpdateForm from "@/components/settings/password-update-form"
import AppLayout from "@/layouts/app-layout"
import SettingsLayout from "@/layouts/settings/layout"
import { Head } from "@inertiajs/react"

export default function Password() {
  return (
    <AppLayout navigation={[]}>
      <Head title="Password settings" />

      <SettingsLayout>
        <PasswordUpdateForm />
      </SettingsLayout>
    </AppLayout>
  )
}
