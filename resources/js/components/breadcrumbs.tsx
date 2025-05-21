import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList } from "@/components/ui/breadcrumb"
import { Button, buttonVariants } from "@/components/ui/button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { useInitials } from "@/hooks/use-initials"
import { cn } from "@/lib/utils"
import { type SharedData } from "@/types"
import { Link, usePage } from "@inertiajs/react"
import { ChevronsUpDownIcon } from "lucide-react"

export function Breadcrumbs() {
  const getInitials = useInitials()
  const { currentTeam } = usePage<SharedData>().props
  return (
    <Breadcrumb>
      <BreadcrumbList>
        <BreadcrumbItem>
          <BreadcrumbLink asChild>
            <Link href={route("team.overview", [currentTeam])} className={cn(buttonVariants({ variant: "ghost" }), "px-2 py-1")}>
              <Avatar className={"size-6 rounded-md"}>
                <AvatarImage src={currentTeam?.logo} />
                <AvatarFallback className={"rounded-md text-xs font-black"}>{getInitials(currentTeam?.name ?? "")}</AvatarFallback>
              </Avatar>
              {currentTeam?.name}
            </Link>
          </BreadcrumbLink>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant={"ghost"} size={"icon"} className={"w-6"}>
                <ChevronsUpDownIcon />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align={"end"}></DropdownMenuContent>
          </DropdownMenu>
        </BreadcrumbItem>
      </BreadcrumbList>
    </Breadcrumb>
  )
}
