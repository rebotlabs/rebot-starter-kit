import { Button } from "@/components/ui/button"
import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { useLang } from "@/hooks/useLang"
import type { Organization } from "@/types"
import { Link } from "@inertiajs/react"
import { Plus } from "lucide-react"

import { OrganizationList } from "./organization-list"

interface OrganizationSelectorProps {
  organizations: Organization[]
}

export function OrganizationSelector({ organizations }: OrganizationSelectorProps) {
  const { __ } = useLang()

  return (
    <div className="space-y-4">
      {organizations.length > 0 ? (
        <>
          <OrganizationList organizations={organizations} />

          <div className="relative">
            <div className="absolute inset-0 flex items-center">
              <div className="border-border w-full border-t" />
            </div>
            <div className="relative flex justify-center text-sm">
              <span className="bg-background text-muted-foreground px-2">{__("organizations.select.or")}</span>
            </div>
          </div>
        </>
      ) : (
        <Card>
          <CardHeader className="text-center">
            <CardTitle>{__("organizations.select.no_organizations")}</CardTitle>
            <CardDescription>{__("organizations.select.no_access")}</CardDescription>
          </CardHeader>
        </Card>
      )}

      <Button asChild className="w-full">
        <Link href={route("onboarding.organization")}>
          <Plus className="mr-2 h-4 w-4" />
          {__("organizations.select.create_new")}
        </Link>
      </Button>
    </div>
  )
}
