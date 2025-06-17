import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { cn } from "@/lib/utils"
import type { Member, SharedData, Organization } from "@/types"
import { useForm, usePage } from "@inertiajs/react"
import { CheckIcon, ChevronsUpDownIcon } from "lucide-react"
import { type FormEventHandler, useRef, useState } from "react"

export const ChangeOwner = () => {
  const { members, organization } = usePage<SharedData & { organization: Organization; members: Member[] }>().props
  const [open, setOpen] = useState<boolean>(false)
  const [changingOwner, setChangingOwner] = useState<boolean>(false)
  const passwordInput = useRef<HTMLInputElement>(null)

  const { data, setData, processing, reset, errors, clearErrors, patch } = useForm<
    Required<{
      password: string
      owner_id: number
    }>
  >({
    password: "",
    owner_id: organization.owner_id,
  })

  const changeOwner: FormEventHandler = (e) => {
    e.preventDefault()

    patch(route("organization.settings.ownership", [organization]), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => passwordInput.current?.focus(),
      onFinish: () => reset(),
    })
  }

  const closeModal = () => {
    clearErrors()
    reset()
    setChangingOwner(false)
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Organization Owner</CardTitle>
        <CardDescription>Manage owner of the organization</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="grid gap-4">
          <p className="text-muted-foreground text-sm">
            The organization owner is the person who has full control over the organization, including billing and settings.
          </p>
          <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
              <Button variant="outline" role="combobox" aria-expanded={open} className="w-[300px] justify-between">
                {members.find((member) => member.user.id === organization.owner_id)?.user?.name ?? "Select new owner"}
                <ChevronsUpDownIcon />
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-[300px] p-0">
              <Command>
                <CommandInput placeholder="Search for a user..." className="h-9" />
                <CommandList>
                  <CommandEmpty>No user found.</CommandEmpty>
                  <CommandGroup>
                    {members.map((member) => (
                      <CommandItem
                        key={member.user.id}
                        value={member.user.name}
                        onSelect={() => {
                          setData("owner_id", member.user.id)
                          setChangingOwner(true)
                        }}
                      >
                        {member.user.name}
                        <CheckIcon className={cn("ml-auto", organization.owner_id === member.user.id ? "opacity-100" : "opacity-0")} />
                      </CommandItem>
                    ))}
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
        </div>
        <Dialog open={changingOwner} onOpenChange={setChangingOwner}>
          <DialogContent>
            <DialogTitle>Are you sure you want to change organization ownership?</DialogTitle>
            <DialogDescription>
              Once you change the organization owner, the new owner will have full control over the organization, including billing and settings. Please confirm your
              action.
            </DialogDescription>
            <form className="space-y-6" onSubmit={changeOwner}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  Password
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder="Password"
                  autoComplete="current-password"
                />

                <InputError message={errors.password} />
              </div>

              <DialogFooter className="gap-2">
                <DialogClose asChild>
                  <Button variant="link" onClick={closeModal}>
                    Cancel
                  </Button>
                </DialogClose>

                <Button disabled={processing} type="submit">
                  Change Owner
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardContent>
    </Card>
  )
}
