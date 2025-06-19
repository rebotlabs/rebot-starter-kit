import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { useInitials } from "@/hooks/use-initials"
import AppLayout from "@/layouts/app-layout"
import type { Organization, SharedData } from "@/types"
import { useTranslations } from "@/utils/translations"
import { Head, Link, usePage } from "@inertiajs/react"
import { Building2, Plus } from "lucide-react"

interface SelectOrganizationProps extends SharedData {
  organizations: Organization[]
}

export default function SelectOrganization() {
  const { organizations } = usePage<SelectOrganizationProps>().props
  const getInitials = useInitials()
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

          <div className="space-y-4">
            {organizations.length > 0 ? (
              <>
                <div className="space-y-2">
                  {organizations.map((organization) => (
                    <Card key={organization.id} className="cursor-pointer transition-shadow hover:shadow-md">
                      <Link href={route("organization.switch", [organization])} method="post" className="block">
                        <CardContent className="flex items-center space-x-4 p-4">
                          <Avatar className="h-10 w-10">
                            <AvatarFallback className="bg-blue-100 text-blue-600">{getInitials(organization.name)}</AvatarFallback>
                          </Avatar>
                          <div className="min-w-0 flex-1">
                            <h3 className="text-foreground truncate font-medium">{organization.name}</h3>
                            <p className="text-muted-foreground text-sm">{organization.slug}</p>
                          </div>
                        </CardContent>
                      </Link>
                    </Card>
                  ))}
                </div>

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
        </div>
      </div>
    </AppLayout>
  )
}
