import { Head } from "@inertiajs/react"

import { VerifyEmailOtpForm } from "@/components/auth/verify-email-otp-form"
import AuthLayout from "@/layouts/auth-layout"

export default function VerifyEmailOtp({ status }: { status?: string }) {
  return (
    <AuthLayout
      title="Verify your email"
      description="We've sent a 6-digit verification code to your email address. Please enter it below to verify your account."
    >
      <Head title="Email verification" />
      <VerifyEmailOtpForm status={status} />
    </AuthLayout>
  )
}
