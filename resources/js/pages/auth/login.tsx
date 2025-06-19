import { Head } from "@inertiajs/react"

import { LoginForm } from "@/components/auth/login-form"
import AuthLayout from "@/layouts/auth-layout"
import { useTranslations } from "@/utils/translations"

interface LoginProps {
  status?: string
  canResetPassword: boolean
}

export default function Login({ status, canResetPassword }: LoginProps) {
  const { __ } = useTranslations()

  return (
    <AuthLayout title={__("auth.login.welcome_back")} description={__("auth.login.description")}>
      <Head title={__("auth.login.title")} />
      <LoginForm status={status} canResetPassword={canResetPassword} />
    </AuthLayout>
  )
}
