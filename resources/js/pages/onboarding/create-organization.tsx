import { CreateOrganizationForm } from "@/components/onboarding/create-organization-form"
import { useTranslation } from "@/hooks/use-i18n"
import AuthLayout from "@/layouts/auth-layout"
import { Head } from "@inertiajs/react"

export default function CreateOrganization() {
  const t = useTranslation()

  return (
    <AuthLayout title={t("organizations.create.title")} description={t("organizations.create.description")}>
      <Head title={t("organizations.create.page_title")} />
      <CreateOrganizationForm />
    </AuthLayout>
  )
}
