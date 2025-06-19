import { Head } from "@inertiajs/react"

import { VerifyEmailForm } from "@/components/auth/verify-email-form"
import { useLang } from "@/hooks/useLang"
import AuthLayout from "@/layouts/auth-layout"

export default function VerifyEmail({ status }: { status?: string }) {
  const { __ } = useLang()

  return (
    <AuthLayout title={__("auth.verify_email.title")} description={__("auth.verify_email.description")}>
      <Head title={__("auth.verify_email.title")} />
      <VerifyEmailForm status={status} />
    </AuthLayout>
  )
}
