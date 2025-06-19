import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import { FormEventHandler } from "react"

import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useTranslations } from "@/utils/translations"

type LoginForm = {
  email: string
  password: string
  remember: boolean
}

interface LoginFormProps {
  status?: string
  canResetPassword: boolean
}

export function LoginForm({ status, canResetPassword }: LoginFormProps) {
  const { __ } = useTranslations()
  const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
    email: "",
    password: "",
    remember: false,
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route("login"), {
      onFinish: () => reset("password"),
      onError: (errors) => {
        if (errors.two_factor_required) {
          // Redirect to 2FA challenge page
          window.location.href = route("two-factor.login")
        }
      },
    })
  }

  return (
    <>
      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-2">
            <Label htmlFor="email">{__("auth.labels.email")}</Label>
            <Input
              id="email"
              type="email"
              required
              autoFocus
              tabIndex={1}
              autoComplete="email"
              value={data.email}
              onChange={(e) => setData("email", e.target.value)}
              placeholder={__("auth.placeholders.email")}
            />
            <InputError message={errors.email} />
          </div>

          <div className="grid gap-2">
            <div className="flex items-center">
              <Label htmlFor="password">{__("auth.labels.password")}</Label>
              {canResetPassword && (
                <TextLink href={route("password.request")} className="ml-auto text-sm" tabIndex={5}>
                  {__("auth.links.forgot_password")}
                </TextLink>
              )}
            </div>
            <Input
              id="password"
              type="password"
              required
              tabIndex={2}
              autoComplete="current-password"
              value={data.password}
              onChange={(e) => setData("password", e.target.value)}
              placeholder={__("auth.placeholders.password")}
            />
            <InputError message={errors.password} />
          </div>

          <div className="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" checked={data.remember} onClick={() => setData("remember", !data.remember)} tabIndex={3} />
            <Label htmlFor="remember">{__("auth.labels.remember_me")}</Label>
          </div>

          <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            {__("auth.buttons.log_in")}
          </Button>
        </div>

        <div className="text-muted-foreground text-center text-sm">
          {__("auth.links.no_account")}{" "}
          <TextLink href={route("register")} tabIndex={5}>
            {__("auth.buttons.register")}
          </TextLink>
        </div>
      </form>

      {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
    </>
  )
}
