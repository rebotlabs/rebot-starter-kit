import { Head } from "@inertiajs/react"

import { TwoFactorChallengeForm } from "@/components/auth/two-factor-challenge-form"
import { useLang } from "@/hooks/useLang"
import AuthLayout from "@/layouts/auth-layout"

export default function TwoFactorChallenge() {
  const { __ } = useLang()

  return (
    <AuthLayout title={__("auth.two_factor.title")} description={__("auth.two_factor.description")}>
      <Head title={__("auth.two_factor.title")} />
      <TwoFactorChallengeForm />
    </AuthLayout>
  )
}
