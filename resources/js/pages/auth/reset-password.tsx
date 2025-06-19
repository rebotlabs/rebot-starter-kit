import { Head } from "@inertiajs/react"

import { ResetPasswordForm } from "@/components/auth/reset-password-form"
import AuthLayout from "@/layouts/auth-layout"

interface ResetPasswordProps {
  token: string
  email: string
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
  return (
    <AuthLayout title="Reset password" description="Please enter your new password below">
      <Head title="Reset password" />
      <ResetPasswordForm token={token} email={email} />
    </AuthLayout>
  )
}
