import { Head } from "@inertiajs/react"

import { TwoFactorChallengeForm } from "@/components/auth/two-factor-challenge-form"
import { useTranslation } from "@/hooks/use-i18n"
import AuthLayout from "@/layouts/auth-layout"

export default function TwoFactorChallenge() {
  const t = useTranslation()

  return (
    <AuthLayout title={t("auth.two_factor.title")} description={t("auth.two_factor.description")}>
      <Head title={t("auth.two_factor.title")} />
      <TwoFactorChallengeForm />
    </AuthLayout>
  )
}
