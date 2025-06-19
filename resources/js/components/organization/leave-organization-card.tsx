import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import type { Organization } from "@/types"
import { useTranslations } from "@/utils/translations"
import { useForm } from "@inertiajs/react"
import { type FormEventHandler, useRef } from "react"

interface LeaveOrganizationCardProps {
  organization: Organization
}

export function LeaveOrganizationCard({ organization }: LeaveOrganizationCardProps) {
  const { __ } = useTranslations()
  const passwordInput = useRef<HTMLInputElement>(null)

  const { data, setData, processing, reset, errors, clearErrors, post } = useForm<{
    password: string
  }>({
    password: "",
  })

  const leaveOrganization: FormEventHandler = (e) => {
    e.preventDefault()

    post(route("organization.settings.member.leave", [organization]), {
      preserveScroll: true,
      onError: () => passwordInput.current?.focus(),
      onFinish: () => reset(),
    })
  }

  const closeModal = () => {
    clearErrors()
    reset()
  }

  return (
    <Card variant="destructive">
      <CardHeader>
        <CardTitle>{__("organizations.leave.card_title")}</CardTitle>
        <CardDescription>{__("organizations.leave.card_description")}</CardDescription>
      </CardHeader>

      <CardContent>
        <div className="relative space-y-0.5">
          <p className="font-medium">{__("ui.actions.warning")}</p>
          <p className="text-sm">{__("organizations.leave.card_warning")}</p>
        </div>
      </CardContent>

      <CardFooter className="justify-end">
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="destructive">{__("organizations.leave.button")}</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogTitle>{__("organizations.leave.confirm_dialog_title")}</DialogTitle>
            <DialogDescription dangerouslySetInnerHTML={{ __html: __("organizations.leave.confirm_dialog_description", { name: organization.name }) }} />
            <form className="space-y-6" onSubmit={leaveOrganization}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  {__("organizations.leave.password_label")}
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder={__("organizations.leave.password_placeholder")}
                  autoComplete="current-password"
                />

                <InputError message={errors.password} />
              </div>

              <DialogFooter className="gap-2">
                <DialogClose asChild>
                  <Button variant="link" onClick={closeModal}>
                    {__("ui.actions.cancel")}
                  </Button>
                </DialogClose>

                <Button variant="destructive" type="submit" disabled={processing}>
                  {__("organizations.leave.button")}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardFooter>
    </Card>
  )
}
