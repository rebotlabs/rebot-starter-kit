import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useTranslation } from "@/hooks/use-i18n"
import type { Organization } from "@/types"
import { useForm } from "@inertiajs/react"
import { type FormEventHandler, useRef } from "react"

interface LeaveOrganizationCardProps {
  organization: Organization
}

export function LeaveOrganizationCard({ organization }: LeaveOrganizationCardProps) {
  const t = useTranslation()
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
        <CardTitle>{t("organizations.leave.card_title")}</CardTitle>
        <CardDescription>{t("organizations.leave.card_description")}</CardDescription>
      </CardHeader>

      <CardContent>
        <div className="relative space-y-0.5">
          <p className="font-medium">{t("ui.actions.warning")}</p>
          <p className="text-sm">{t("organizations.leave.card_warning")}</p>
        </div>
      </CardContent>

      <CardFooter className="justify-end">
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="destructive">{t("organizations.leave.button")}</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogTitle>{t("organizations.leave.confirm_dialog_title")}</DialogTitle>
            <DialogDescription
              dangerouslySetInnerHTML={{ __html: t("organizations.leave.confirm_dialog_description", { name: organization.name }) }}
            />
            <form className="space-y-6" onSubmit={leaveOrganization}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  {t("organizations.leave.password_label")}
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder={t("organizations.leave.password_placeholder")}
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

                <Button variant="destructive" type="submit" disabled={processing}>
                  {t("organizations.leave.button")}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardFooter>
    </Card>
  )
}
