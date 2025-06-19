import InputError from "@/components/input-error"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useLang } from "@/hooks/useLang"
import type { Invitation } from "@/types"
import { useForm } from "@inertiajs/react"
import { LockIcon } from "lucide-react"
import { type FormEventHandler } from "react"

type LoginForm = {
  email: string
  password: string
}

interface InvitationLoginFormProps {
  invitation: Invitation & {
    organization: {
      id: number
      name: string
    }
  }
  onCancel: () => void
}

export function InvitationLoginForm({ invitation, onCancel }: InvitationLoginFormProps) {
  const { __ } = useLang()

  const {
    data: loginData,
    setData: setLoginData,
    post: postLogin,
    processing: loginProcessing,
    errors: loginErrors,
  } = useForm<LoginForm>({
    email: invitation.email,
    password: "",
  })

  const handleLogin: FormEventHandler = (e) => {
    e.preventDefault()
    postLogin(route("login"), {
      onSuccess: () => onCancel(),
    })
  }

  return (
    <div className="space-y-4">
      <Alert>
        <LockIcon className="h-4 w-4" />
        <AlertDescription>{__("invitations.alerts.login_to_accept")}</AlertDescription>
      </Alert>

      <form onSubmit={handleLogin} className="space-y-4">
        <div>
          <Label htmlFor="login-email">{__("invitations.form.email")}</Label>
          <Input
            id="login-email"
            type="email"
            value={loginData.email}
            onChange={(e) => setLoginData("email", e.target.value)}
            required
            disabled={loginProcessing}
            readOnly
            className="bg-muted"
          />
          <InputError className="mt-2" message={loginErrors.email} />
        </div>

        <div>
          <Label htmlFor="login-password">{__("invitations.form.password")}</Label>
          <Input
            id="login-password"
            type="password"
            value={loginData.password}
            onChange={(e) => setLoginData("password", e.target.value)}
            required
            disabled={loginProcessing}
            placeholder={__("invitations.form.password_login_placeholder")}
          />
          <InputError className="mt-2" message={loginErrors.password} />
        </div>

        <div className="flex space-x-3">
          <Button type="submit" disabled={loginProcessing} className="flex-1">
            <LockIcon className="mr-2 h-4 w-4" />
            {__("invitations.buttons.log_in")}
          </Button>
          <Button type="button" variant="outline" onClick={onCancel} disabled={loginProcessing}>
            {__("invitations.buttons.cancel")}
          </Button>
        </div>
      </form>
    </div>
  )
}
