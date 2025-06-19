import { Head } from "@inertiajs/react"

import { TwoFactorChallengeForm } from "@/components/auth/two-factor-challenge-form"
import AuthLayout from "@/layouts/auth-layout"
import { useTranslations } from "@/utils/translations"

export default function TwoFactorChallenge() {
  const { __ } = useTranslations()

  return (
    <AuthLayout title={__("auth.two_factor.title")} description={__("auth.two_factor.description")}>
      <Head title={__("auth.two_factor.title")} />
      <TwoFactorChallengeForm />
    </AuthLayout>
  )
}
