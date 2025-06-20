import { useForm } from "@inertiajs/react"
import { FormEventHandler, useRef } from "react"

import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { useTranslation } from "@/hooks/use-i18n"

export default function DeleteUser() {
  const t = useTranslation()
  const passwordInput = useRef<HTMLInputElement>(null)
  const {
    data,
    setData,
    delete: destroy,
    processing,
    reset,
    errors,
    clearErrors,
  } = useForm<
    Required<{
      password: string
    }>
  >({ password: "" })

  const deleteUser: FormEventHandler = (e) => {
    e.preventDefault()

    destroy(route("settings.profile.delete"), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
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
        <CardTitle>{t("ui.user.delete_title")}</CardTitle>
        <CardDescription>{t("ui.user.delete_description")}</CardDescription>
      </CardHeader>

      <CardContent>
        <div className="relative space-y-0.5">
          <p className="font-medium">{t("ui.actions.warning")}</p>
          <p className="text-sm">{t("ui.user.delete_warning")}</p>
        </div>
      </CardContent>

      <CardFooter className="justify-end">
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="destructive">{t("ui.user.delete_button")}</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogTitle>{t("ui.user.delete_confirm_title")}</DialogTitle>
            <DialogDescription>{t("ui.user.delete_confirm_description")}</DialogDescription>
            <form className="space-y-6" onSubmit={deleteUser}>
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

                <Button variant="destructive" type="submit" disabled={processing}>
                  {t("ui.user.delete_button")}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardFooter>
    </Card>
  )
}
