import { useForm, usePage } from "@inertiajs/react"
import { FormEventHandler, useRef } from "react"

import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { useLang } from "@/hooks/useLang"
import { SharedData } from "@/types"

export const DeleteOrganization = () => {
  const { __ } = useLang()
  const { currentOrganization } = usePage<SharedData>().props
  const passwordInput = useRef<HTMLInputElement>(null)
  const {
    data,
    setData,
    delete: destroy,
    processing,
    reset,
    errors,
    clearErrors,
  } = useForm<Required<{ password: string }>>({
    password: "",
  })

  const deleteOrganization: FormEventHandler = (e) => {
    e.preventDefault()

    destroy(route("organization.delete", [currentOrganization]), {
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
        <CardTitle>{__("ui.organization.delete_title")}</CardTitle>
        <CardDescription>{__("ui.organization.delete_description")}</CardDescription>
      </CardHeader>

      <CardContent>
        <div className="relative space-y-0.5">
          <p className="font-medium">{__("ui.actions.warning")}</p>
          <p className="text-sm">{__("ui.organization.delete_warning")}</p>
        </div>
      </CardContent>

      <CardFooter className="justify-end">
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="destructive">{__("ui.organization.delete_button")}</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogTitle>{__("ui.organization.delete_confirm_title")}</DialogTitle>
            <DialogDescription>{__("ui.organization.delete_confirm_description")}</DialogDescription>
            <form className="space-y-6" onSubmit={deleteOrganization}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  {__("auth.labels.password")}
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder={__("ui.password.password_placeholder")}
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
                  {__("ui.organization.delete_button")}
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardFooter>
    </Card>
  )
}
