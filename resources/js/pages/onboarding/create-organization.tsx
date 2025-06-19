import { CreateOrganizationForm } from "@/components/onboarding/create-organization-form"
import AuthLayout from "@/layouts/auth-layout"
import { useTranslations } from "@/utils/translations"
import { Head } from "@inertiajs/react"

export default function CreateOrganization() {
  const { __ } = useTranslations()

  return (
    <AuthLayout title={__("organizations.create.title")} description={__("organizations.create.description")}>
      <Head title={__("organizations.create.page_title")} />
      <CreateOrganizationForm />
    </AuthLayout>
  )
}
