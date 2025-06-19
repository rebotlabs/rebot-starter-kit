import { Head } from "@inertiajs/react"

import { TwoFactorChallengeForm } from "@/components/auth/two-factor-challenge-form"
import AuthLayout from "@/layouts/auth-layout"

export default function TwoFactorChallenge() {
  return (
    <AuthLayout title="Two-Factor Authentication" description="Please enter your authentication code to continue">
      <Head title="Two-Factor Authentication" />
      <TwoFactorChallengeForm />
    </AuthLayout>
  )
}
