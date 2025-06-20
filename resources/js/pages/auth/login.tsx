import { Head } from "@inertiajs/react"

import { LoginForm } from "@/components/auth/login-form"
import { useTranslation } from "@/hooks/use-i18n"
import AuthLayout from "@/layouts/auth-layout"

interface LoginProps {
  status?: string
  canResetPassword: boolean
}

export default function Login({ status, canResetPassword }: LoginProps) {
  const t = useTranslation()

  return (
    <AuthLayout title={t("auth.login.welcome_back")} description={t("auth.login.description")}>
      <Head title={t("auth.login.title")} />
      <LoginForm status={status} canResetPassword={canResetPassword} />
    </AuthLayout>
  )
}
