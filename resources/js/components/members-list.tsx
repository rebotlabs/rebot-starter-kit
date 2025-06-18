import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useInitials } from "@/hooks/use-initials"
import type { Member, SharedData } from "@/types"
import { router, usePage } from "@inertiajs/react"
import { LogOutIcon, MoreVerticalIcon, UserMinusIcon } from "lucide-react"
import { useState } from "react"

export const MembersList = () => {
  const { members, auth, currentOrganization } = usePage<SharedData & { members: Member[] }>().props
  const getInitials = useInitials()
  const [memberToRemove, setMemberToRemove] = useState<Member | null>(null)
  const [isRoleUpdating, setIsRoleUpdating] = useState<number | null>(null)

  const handleRoleChange = (member: Member, newRole: string) => {
    if (member.role === newRole) return

    setIsRoleUpdating(member.id)

    router.patch(
      route("organization.settings.members.update-role", [currentOrganization, member]),
      { role: newRole },
      {
        preserveScroll: true,
        onFinish: () => setIsRoleUpdating(null),
      },
    )
  }

  const handleRemoveMember = () => {
    if (!memberToRemove) return

    router.delete(route("organization.settings.members.remove", [currentOrganization, memberToRemove]), {
      preserveScroll: true,
      onFinish: () => setMemberToRemove(null),
    })
  }

  return (
    <>
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
                    {member.user.id === currentOrganization.owner_id && <Badge variant="outline">Owner</Badge>}
                  </span>
                  <span className="text-muted-foreground text-xs">{member.user.email}</span>
                </div>
              </div>
              <div className="col-span-3">
                {member.role === "owner" ? (
                  <Badge variant="outline" className="w-[100px] justify-center">
                    Owner
                  </Badge>
                ) : (
                  <Select
                    value={member.role}
                    disabled={member.user.id === auth.user.id || member.user.id === currentOrganization.owner_id || isRoleUpdating === member.id}
                    onValueChange={(value) => handleRoleChange(member, value)}
                  >
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
                )}
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
                      <DropdownMenuItem
                        variant="destructive"
                        disabled={auth.user.id === currentOrganization.owner_id}
                        onClick={() => (window.location.href = route("organization.settings.leave", [currentOrganization]))}
                      >
                        <LogOutIcon />
                        Leave organization
                      </DropdownMenuItem>
                    ) : (
                      <DropdownMenuItem
                        variant="destructive"
                        disabled={member.user.id === currentOrganization.owner_id}
                        onClick={() => setMemberToRemove(member)}
                      >
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

      <Dialog open={!!memberToRemove} onOpenChange={() => setMemberToRemove(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Remove Member</DialogTitle>
            <DialogDescription>
              Are you sure you want to remove <strong>{memberToRemove?.user.name}</strong> from this organization? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setMemberToRemove(null)}>
              Cancel
            </Button>
            <Button variant="destructive" onClick={handleRemoveMember}>
              Remove Member
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  )
}
