import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList } from "@/components/ui/breadcrumb"
import { Button, buttonVariants } from "@/components/ui/button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { useInitials } from "@/hooks/use-initials"
import { useLang } from "@/hooks/useLang"
import { cn } from "@/lib/utils"
import { type SharedData } from "@/types"
import { Link, router, usePage } from "@inertiajs/react"
import { CheckIcon, ChevronsUpDownIcon, Plus } from "lucide-react"
import { useState } from "react"

import { CreateOrganizationModal } from "./organization/create-organization-modal"

export function Breadcrumbs() {
  const { __ } = useLang()
  const getInitials = useInitials()
  const { currentOrganization, organizations } = usePage<SharedData>().props
  const [createModalOpen, setCreateModalOpen] = useState(false)

  const handleOrganizationSwitch = (organizationId: number) => {
    const organization = organizations.find((org) => org.id === organizationId)
    if (organization && organization.id !== currentOrganization?.id) {
      router.post(route("organization.switch", [organization]))
    }
  }

  return (
    <>
      <Breadcrumb>
        <BreadcrumbList>
          <BreadcrumbItem>
            <BreadcrumbLink asChild>
              <Link href={route("organization.overview", [currentOrganization])} className={cn(buttonVariants({ variant: "ghost" }), "px-2 py-1")}>
                <Avatar className="size-6 rounded-md">
                  <AvatarImage src={currentOrganization?.logo} />
                  <AvatarFallback className="rounded-md text-xs font-black">{getInitials(currentOrganization?.name ?? "")}</AvatarFallback>
                </Avatar>
                {currentOrganization?.name}
              </Link>
            </BreadcrumbLink>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="w-6">
                  <ChevronsUpDownIcon />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-64">
                {organizations.map((organization) => (
                  <DropdownMenuItem
                    key={organization.id}
                    onClick={() => handleOrganizationSwitch(organization.id)}
                    className="flex cursor-pointer items-center gap-3 p-3"
                  >
                    <Avatar className="size-8 rounded-md">
                      <AvatarImage src={organization.logo} />
                      <AvatarFallback className="rounded-md text-xs font-medium">{getInitials(organization.name)}</AvatarFallback>
                    </Avatar>
                    <div className="min-w-0 flex-1">
                      <div className="truncate font-medium">{organization.name}</div>
                      <div className="text-muted-foreground truncate text-sm">{organization.slug}</div>
                    </div>
                    {organization.id === currentOrganization?.id && <CheckIcon className="text-primary size-4" />}
                  </DropdownMenuItem>
                ))}
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={() => setCreateModalOpen(true)} className="flex cursor-pointer items-center gap-3 p-3">
                  <div className="bg-muted flex size-8 items-center justify-center rounded-md">
                    <Plus className="size-4" />
                  </div>
                  <div className="flex-1">
                    <div className="font-medium">{__("organizations.select.create_new")}</div>
                  </div>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </BreadcrumbItem>
        </BreadcrumbList>
      </Breadcrumb>

      <CreateOrganizationModal open={createModalOpen} onOpenChange={setCreateModalOpen} />
    </>
  )
}
