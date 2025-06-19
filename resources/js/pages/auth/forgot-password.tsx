import { Head } from "@inertiajs/react"

import { ForgotPasswordForm } from "@/components/auth/forgot-password-form"
import AuthLayout from "@/layouts/auth-layout"

export default function ForgotPassword({ status }: { status?: string }) {
  return (
    <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
      <Head title="Forgot password" />
      <ForgotPasswordForm status={status} />
    </AuthLayout>
  )
}
