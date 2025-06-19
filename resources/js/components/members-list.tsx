import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useInitials } from "@/hooks/use-initials"
import type { Member, SharedData } from "@/types"
import { useTranslations } from "@/utils/translations"
import { router, usePage } from "@inertiajs/react"
import { LogOutIcon, MoreVerticalIcon, UserMinusIcon } from "lucide-react"
import { useState } from "react"

export const MembersList = () => {
  const { __ } = useTranslations()
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
                  <div className="border-input bg-background ring-offset-background flex h-10 w-[100px] items-center justify-center rounded-md border px-3 py-2 text-sm">
                    {__("ui.roles.owner")}
                  </div>
                ) : (
                  <Select
                    value={member.role}
                    disabled={member.user.id === auth.user.id || member.user.id === currentOrganization.owner_id || isRoleUpdating === member.id}
                    onValueChange={(value) => handleRoleChange(member, value)}
                  >
                    <SelectTrigger className="w-[100px]" aria-label={__("organizations.members.role_label")}>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent align="end">
                      <SelectGroup>
                        <SelectLabel>{__("ui.roles.role")}</SelectLabel>
                        <SelectItem value="admin">{__("ui.roles.admin")}</SelectItem>
                        <SelectItem value="member">{__("ui.roles.member")}</SelectItem>
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
                        {__("organizations.members.leave_action")}
                      </DropdownMenuItem>
                    ) : (
                      <DropdownMenuItem
                        variant="destructive"
                        disabled={member.user.id === currentOrganization.owner_id}
                        onClick={() => setMemberToRemove(member)}
                      >
                        <UserMinusIcon />
                        {__("ui.actions.remove")}
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
            <DialogTitle>{__("ui.members.remove_title")}</DialogTitle>
            <DialogDescription>{memberToRemove && __("ui.members.remove_description", { name: memberToRemove.user.name })}</DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setMemberToRemove(null)}>
              {__("ui.actions.cancel")}
            </Button>
            <Button variant="destructive" onClick={handleRemoveMember}>
              {__("ui.members.remove_button")}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  )
}
