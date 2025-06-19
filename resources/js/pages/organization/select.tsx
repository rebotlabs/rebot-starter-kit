import AppLayout from "@/layouts/app-layout"
import type { Organization, SharedData } from "@/types"
import { useTranslations } from "@/utils/translations"
import { Head, usePage } from "@inertiajs/react"
import { Building2 } from "lucide-react"

import { OrganizationSelector } from "@/components/organization/organization-selector"

interface SelectOrganizationProps extends SharedData {
  organizations: Organization[]
}

export default function SelectOrganization() {
  const { organizations } = usePage<SelectOrganizationProps>().props
  const { __ } = useTranslations()

  return (
    <AppLayout navigation={[]}>
      <Head title={__("organizations.select.title")} />

      <div className="bg-background flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div className="w-full max-w-lg space-y-8">
          <div className="text-center">
            <Building2 className="text-muted-foreground mx-auto h-12 w-12" />
            <h1 className="text-foreground mt-4 text-3xl font-bold">{__("organizations.select.title")}</h1>
            <p className="text-muted-foreground mt-2">{__("organizations.select.subtitle")}</p>
          </div>

          <OrganizationSelector organizations={organizations} />
        </div>
      </div>
    </AppLayout>
  )
}
