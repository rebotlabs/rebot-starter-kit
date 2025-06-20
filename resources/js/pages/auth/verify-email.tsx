import { Head } from "@inertiajs/react"

import { VerifyEmailForm } from "@/components/auth/verify-email-form"
import { useTranslation } from "@/hooks/use-i18n"
import AuthLayout from "@/layouts/auth-layout"

export default function VerifyEmail({ status }: { status?: string }) {
  const t = useTranslation()

  return (
    <AuthLayout title={t("auth.verify_email.title")} description={t("auth.verify_email.description")}>
      <Head title={t("auth.verify_email.title")} />
      <VerifyEmailForm status={status} />
    </AuthLayout>
  )
}
