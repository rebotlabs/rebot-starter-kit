import { Head } from "@inertiajs/react"

import { LoginForm } from "@/components/auth/login-form"
import { useLang } from "@/hooks/useLang"
import AuthLayout from "@/layouts/auth-layout"

interface LoginProps {
  status?: string
  canResetPassword: boolean
}

export default function Login({ status, canResetPassword }: LoginProps) {
  const { __ } = useLang()

  return (
    <AuthLayout title={__("auth.login.welcome_back")} description={__("auth.login.description")}>
      <Head title={__("auth.login.title")} />
      <LoginForm status={status} canResetPassword={canResetPassword} />
    </AuthLayout>
  )
}
