import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useInitials } from "@/hooks/use-initials"
import type { Member, SharedData } from "@/types"
import { usePage } from "@inertiajs/react"
import { LogOutIcon, MoreVerticalIcon, UserMinusIcon } from "lucide-react"

export const MembersList = () => {
  const { members, auth, currentTeam } = usePage<SharedData & { members: Member[] }>().props
  const getInitials = useInitials()

  return (
    <div className="grid gap-4">
      <h2 className="text-sm leading-none font-medium select-none">Members</h2>
      <ul className="grid gap-2">
        {members.map((member) => (
          <li key={member.id} className="grid grid-cols-12">
            <div className="col-span-8 flex items-center gap-2">
              <Avatar>
                <AvatarFallback>{getInitials(member.user.name)}</AvatarFallback>
              </Avatar>
              <div className="grid">
                <span className="flex items-center gap-2 text-sm font-semibold">
                  {member.user.name}
                  {member.user.id === auth.user.id && <Badge variant="secondary">You</Badge>}
                </span>
                <span className="text-muted-foreground text-xs">{member.user.email}</span>
              </div>
            </div>
            <div className="col-span-3">
              <Select value={member.role} disabled={member.user.id === auth.user.id}>
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
            <div className="flex items-center justify-end">
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="icon">
                    <MoreVerticalIcon />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  {member.user.id === auth.user.id ? (
                    <DropdownMenuItem variant="destructive" disabled={auth.user.id === currentTeam.owner_id}>
                      <LogOutIcon />
                      Leave team
                    </DropdownMenuItem>
                  ) : (
                    <DropdownMenuItem variant="destructive">
                      <UserMinusIcon />
                      Remove
                    </DropdownMenuItem>
                  )}
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </li>
        ))}
      </ul>
    </div>
  )
}
