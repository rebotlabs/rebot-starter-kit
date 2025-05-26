import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { Invitation } from "@/types"
import { usePage } from "@inertiajs/react"
import { MoreVerticalIcon, SendIcon, TrashIcon } from "lucide-react"

export const Invitations = () => {
  const { invitations } = usePage<{ invitations: Invitation[] }>().props

  return (
    <div className="grid gap-4">
      <h2 className="text-sm leading-none font-medium select-none">Invitations</h2>
      {invitations.length > 0 ? (
        <ul className="grid gap-2">
          {invitations.map((invitation) => (
            <li key={invitation.id} className="grid grid-cols-12">
              <div className="col-span-6">
                {invitation.user ? (
                  <div className="grid gap-0.5">
                    <span className="text-sm font-semibold">{invitation.user.name}</span>
                    <span className="text-muted-foreground text-xs">{invitation.email}</span>
                  </div>
                ) : (
                  <span className="text-sm font-semibold">{invitation.email}</span>
                )}
              </div>
              <div className="col-span-3 flex items-center justify-end">
                <Select value={invitation.role}>
                  <SelectTrigger className="w-[100px]" aria-label="User's role">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent align="end">
                    <SelectGroup>
                      <SelectLabel>Role</SelectLabel>
                      <SelectItem value="admin">Admin</SelectItem>
                      <SelectItem value="member">Member</SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>
              <div className="col-span-2 flex items-center justify-end">
                <Badge>{invitation.status}</Badge>
              </div>
              <div className="flex items-center justify-end">
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon">
                      <MoreVerticalIcon />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem>
                      <SendIcon />
                      Resend
                    </DropdownMenuItem>
                    <DropdownMenuItem variant="destructive">
                      <TrashIcon />
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </li>
          ))}
        </ul>
      ) : (
        <p>No invitations found.</p>
      )}
    </div>
  )
}
