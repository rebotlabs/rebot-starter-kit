import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { useTranslation } from "@/hooks/use-i18n"
import { cn } from "@/lib/utils"
import type { Member, Organization, SharedData } from "@/types"
import { useForm, usePage } from "@inertiajs/react"
import { CheckIcon, ChevronsUpDownIcon } from "lucide-react"
import { type FormEventHandler, useRef, useState } from "react"

export const ChangeOwner = () => {
  const t = useTranslation()
  const { members, organization } = usePage<SharedData & { organization: Organization; members: Member[] }>().props
  const [open, setOpen] = useState<boolean>(false)
  const [changingOwner, setChangingOwner] = useState<boolean>(false)
  const passwordInput = useRef<HTMLInputElement>(null)

  const { data, setData, processing, reset, errors, clearErrors, patch } = useForm<
    Required<{
      password: string
      member_id: number
    }>
  >({
    password: "",
    member_id: 0,
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
        <CardTitle>{t("ui.organization.owner_title")}</CardTitle>
        <CardDescription>{t("ui.organization.owner_description")}</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="grid gap-4">
          <p className="text-muted-foreground text-sm">{t("ui.organization.owner_info")}</p>
          <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
              <Button variant="outline" role="combobox" aria-expanded={open} className="w-[300px] justify-between">
                {members.find((member) => member.user.id === organization.owner_id)?.user?.name ?? t("ui.organization.select_owner")}
                <ChevronsUpDownIcon />
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-[300px] p-0">
              <Command>
                <CommandInput placeholder={t("ui.organization.search_user")} className="h-9" />
                <CommandList>
                  <CommandEmpty>No user found.</CommandEmpty>
                  <CommandGroup>
                    {members.map((member) => (
                      <CommandItem
                        key={member.user.id}
                        value={member.user.name}
                        onSelect={() => {
                          setData("member_id", member.id)
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
            <DialogTitle>{t("ui.organization.change_owner_title")}</DialogTitle>
            <DialogDescription>{t("ui.organization.change_owner_description")}</DialogDescription>
            <form className="space-y-6" onSubmit={changeOwner}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  {t("auth.labels.password")}
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder={t("ui.password.password_placeholder")}
                  autoComplete="current-password"
                />

                <InputError message={errors.password} />
              </div>

              <DialogFooter className="gap-2">
                <DialogClose asChild>
                  <Button variant="link" onClick={closeModal}>
                    {t("ui.actions.cancel")}
                  </Button>
                </DialogClose>

                <Button disabled={processing} type="submit">
                  {t("ui.organization.change_owner_button")}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardContent>
    </Card>
  )
}
