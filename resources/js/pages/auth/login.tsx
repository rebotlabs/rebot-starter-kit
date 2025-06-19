import { Head } from "@inertiajs/react"

import { LoginForm } from "@/components/auth/login-form"
import AuthLayout from "@/layouts/auth-layout"

interface LoginProps {
  status?: string
  canResetPassword: boolean
}

export default function Login({ status, canResetPassword }: LoginProps) {
  return (
    <AuthLayout title="Log in to your account" description="Enter your email and password below to log in">
      <Head title="Log in" />
      <LoginForm status={status} canResetPassword={canResetPassword} />
    </AuthLayout>
  )
}
