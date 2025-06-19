import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import { FormEventHandler } from "react"

import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useLang } from "@/hooks/useLang"

interface ResetPasswordFormProps {
  token: string
  email: string
}

type ResetPasswordForm = {
  token: string
  email: string
  password: string
  password_confirmation: string
}

export function ResetPasswordForm({ token, email }: ResetPasswordFormProps) {
  const { __ } = useLang()
  const { data, setData, post, processing, errors, reset } = useForm<Required<ResetPasswordForm>>({
    token: token,
    email: email,
    password: "",
    password_confirmation: "",
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route("password.store"), {
      onFinish: () => reset("password", "password_confirmation"),
    })
  }

  return (
    <form onSubmit={submit}>
      <div className="grid gap-6">
        <div className="grid gap-2">
          <Label htmlFor="email">{__("auth.labels.email")}</Label>
          <Input
            id="email"
            type="email"
            name="email"
            autoComplete="email"
            value={data.email}
            className="mt-1 block w-full"
            readOnly
            onChange={(e) => setData("email", e.target.value)}
          />
          <InputError message={errors.email} className="mt-2" />
        </div>

        <div className="grid gap-2">
          <Label htmlFor="password">{__("auth.labels.password")}</Label>
          <Input
            id="password"
            type="password"
            name="password"
            autoComplete="new-password"
            value={data.password}
            className="mt-1 block w-full"
            autoFocus
            onChange={(e) => setData("password", e.target.value)}
            placeholder={__("ui.password.password_placeholder")}
          />
          <InputError message={errors.password} />
        </div>

        <div className="grid gap-2">
          <Label htmlFor="password_confirmation">{__("ui.password.confirm")}</Label>
          <Input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autoComplete="new-password"
            value={data.password_confirmation}
            className="mt-1 block w-full"
            onChange={(e) => setData("password_confirmation", e.target.value)}
            placeholder={__("ui.password.confirm_placeholder")}
          />
          <InputError message={errors.password_confirmation} className="mt-2" />
        </div>

        <Button type="submit" className="mt-4 w-full" disabled={processing}>
          {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
          {__("ui.password.reset")}
        </Button>
      </div>
    </form>
  )
}
