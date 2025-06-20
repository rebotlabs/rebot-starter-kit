import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import { FormEventHandler } from "react"

import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useTranslation } from "@/hooks/use-i18n"

interface ForgotPasswordFormProps {
  status?: string
}

export function ForgotPasswordForm({ status }: ForgotPasswordFormProps) {
  const t = useTranslation()
  const { data, setData, post, processing, errors } = useForm<Required<{ email: string }>>({
    email: "",
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    post(route("password.email"))
  }

  return (
    <>
      {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}

      <div className="space-y-6">
        <form onSubmit={submit}>
          <div className="grid gap-2">
            <Label htmlFor="email">{t("auth.labels.email")}</Label>
            <Input
              id="email"
              type="email"
              name="email"
              autoComplete="off"
              value={data.email}
              autoFocus
              onChange={(e) => setData("email", e.target.value)}
              placeholder={t("auth.placeholders.email")}
            />

            <InputError message={errors.email} />
          </div>

          <div className="my-6 flex items-center justify-start">
            <Button className="w-full" disabled={processing}>
              {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
              {t("auth.buttons.send_reset_link")}
            </Button>
          </div>
        </form>

        <div className="text-muted-foreground space-x-1 text-center text-sm">
          <span>{t("auth.links.remember_password")}</span>
          <TextLink href={route("login")}>{t("auth.buttons.log_in")}</TextLink>
        </div>
      </div>
    </>
  )
}
