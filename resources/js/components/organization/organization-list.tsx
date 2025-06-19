import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Card, CardContent } from "@/components/ui/card"
import { useInitials } from "@/hooks/use-initials"
import type { Organization } from "@/types"
import { Link } from "@inertiajs/react"

interface OrganizationListProps {
  organizations: Organization[]
}

export function OrganizationList({ organizations }: OrganizationListProps) {
  const getInitials = useInitials()

  if (organizations.length === 0) {
    return null
  }

  return (
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
  )
}
