import InputError from "@/components/input-error"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Separator } from "@/components/ui/separator"
import type { Invitation } from "@/types"
import { useTranslations } from "@/utils/translations"
import { Head, useForm } from "@inertiajs/react"
import { CheckCircleIcon, LockIcon, MailIcon, UserIcon, XCircleIcon } from "lucide-react"
import { type FormEventHandler, useState } from "react"

interface InvitationHandleProps {
  invitation: Invitation & {
    organization: {
      id: number
      name: string
    }
  }
  existingUser: boolean
  isAuthenticated: boolean
  currentUserEmail?: string
}

type AcceptForm = {
  name: string
  password: string
  password_confirmation: string
}

type LoginForm = {
  email: string
  password: string
}

export default function InvitationHandle({ invitation, existingUser, isAuthenticated, currentUserEmail }: InvitationHandleProps) {
  const [mode, setMode] = useState<"view" | "register" | "login">("view")
  const { __ } = useTranslations()

  const {
    data: acceptData,
    setData: setAcceptData,
    post: postAccept,
    processing: acceptProcessing,
    errors: acceptErrors,
  } = useForm<AcceptForm>({
    name: "",
    password: "",
    password_confirmation: "",
  })

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

  const { post: postReject, processing: rejectProcessing } = useForm()

  const handleAccept: FormEventHandler = (e) => {
    e.preventDefault()

    if (existingUser && (!isAuthenticated || currentUserEmail !== invitation.email)) {
      setMode("login")
      return
    }

    postAccept(route("invitation.accept", [invitation.accept_token]))
  }

  const handleLogin: FormEventHandler = (e) => {
    e.preventDefault()
    postLogin(route("login"), {
      onSuccess: () => setMode("view"),
    })
  }

  const handleReject = () => {
    postReject(route("invitation.reject", [invitation.reject_token]))
  }

  const canDirectlyAccept = !existingUser || (isAuthenticated && currentUserEmail === invitation.email)

  return (
    <>
      <Head title={__("invitations.page.title")} />

      <div className="bg-background flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div className="w-full max-w-md space-y-8">
          <div className="text-center">
            <h1 className="text-foreground text-3xl font-bold">{__("invitations.page.youre_invited")}</h1>
            <p className="text-muted-foreground mt-2">
              {__("invitations.page.join_and_collaborate", { organization: invitation.organization.name })}
            </p>
          </div>

          <Card>
            <CardHeader className="text-center">
              <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                <MailIcon className="h-6 w-6 text-blue-600" />
              </div>
              <CardTitle>{__("invitations.details.title")}</CardTitle>
              <CardDescription>{__("invitations.details.invited_as_role", { role: invitation.role })}</CardDescription>
            </CardHeader>

            <CardContent className="space-y-6">
              <div className="space-y-2 text-center">
                <p className="text-muted-foreground text-sm">{__("invitations.details.organization")}</p>
                <p className="font-semibold">{invitation.organization.name}</p>
                <p className="text-muted-foreground text-sm">{__("invitations.details.email")}</p>
                <p className="font-semibold">{invitation.email}</p>
                <p className="text-muted-foreground text-sm">{__("invitations.details.role")}</p>
                <p className="font-semibold capitalize">{invitation.role}</p>
              </div>

              <Separator />

              {mode === "view" && (
                <div className="space-y-4">
                  {existingUser && !isAuthenticated && (
                    <Alert>
                      <UserIcon className="h-4 w-4" />
                      <AlertDescription>{__("invitations.alerts.account_exists")}</AlertDescription>
                    </Alert>
                  )}

                  {existingUser && isAuthenticated && currentUserEmail !== invitation.email && (
                    <Alert>
                      <UserIcon className="h-4 w-4" />
                      <AlertDescription>{__("invitations.alerts.different_email", { email: invitation.email })}</AlertDescription>
                    </Alert>
                  )}

                  {!existingUser && (
                    <form onSubmit={handleAccept} className="space-y-4">
                      <div>
                        <Label htmlFor="name">{__("invitations.form.full_name")}</Label>
                        <Input
                          id="name"
                          type="text"
                          value={acceptData.name}
                          onChange={(e) => setAcceptData("name", e.target.value)}
                          required
                          disabled={acceptProcessing}
                          placeholder={__("invitations.form.full_name_placeholder")}
                        />
                        <InputError className="mt-2" message={acceptErrors.name} />
                      </div>

                      <div>
                        <Label htmlFor="password">{__("invitations.form.password")}</Label>
                        <Input
                          id="password"
                          type="password"
                          value={acceptData.password}
                          onChange={(e) => setAcceptData("password", e.target.value)}
                          required
                          disabled={acceptProcessing}
                          placeholder={__("invitations.form.password_placeholder")}
                        />
                        <InputError className="mt-2" message={acceptErrors.password} />
                      </div>

                      <div>
                        <Label htmlFor="password_confirmation">{__("invitations.form.confirm_password")}</Label>
                        <Input
                          id="password_confirmation"
                          type="password"
                          value={acceptData.password_confirmation}
                          onChange={(e) => setAcceptData("password_confirmation", e.target.value)}
                          required
                          disabled={acceptProcessing}
                          placeholder={__("invitations.form.confirm_password_placeholder")}
                        />
                        <InputError className="mt-2" message={acceptErrors.password_confirmation} />
                      </div>

                      <div className="flex space-x-3">
                        <Button type="submit" disabled={acceptProcessing} className="flex-1">
                          <CheckCircleIcon className="mr-2 h-4 w-4" />
                          {__("invitations.buttons.create_account_accept")}
                        </Button>
                        <Button type="button" variant="outline" onClick={handleReject} disabled={rejectProcessing}>
                          <XCircleIcon className="mr-2 h-4 w-4" />
                          {__("invitations.buttons.reject")}
                        </Button>
                      </div>
                    </form>
                  )}

                  {canDirectlyAccept && existingUser && (
                    <div className="flex space-x-3">
                      <Button onClick={handleAccept} disabled={acceptProcessing} className="flex-1">
                        <CheckCircleIcon className="mr-2 h-4 w-4" />
                        {__("invitations.buttons.accept_invitation")}
                      </Button>
                      <Button variant="outline" onClick={handleReject} disabled={rejectProcessing}>
                        <XCircleIcon className="mr-2 h-4 w-4" />
                        {__("invitations.buttons.reject")}
                      </Button>
                    </div>
                  )}

                  {existingUser && (!isAuthenticated || currentUserEmail !== invitation.email) && (
                    <div className="flex space-x-3">
                      <Button onClick={() => setMode("login")} className="flex-1">
                        <LockIcon className="mr-2 h-4 w-4" />
                        {__("invitations.buttons.log_in_to_accept")}
                      </Button>
                      <Button variant="outline" onClick={handleReject} disabled={rejectProcessing}>
                        <XCircleIcon className="mr-2 h-4 w-4" />
                        {__("invitations.buttons.reject")}
                      </Button>
                    </div>
                  )}
                </div>
              )}

              {mode === "login" && (
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
                      <Button type="button" variant="outline" onClick={() => setMode("view")} disabled={loginProcessing}>
                        {__("invitations.buttons.cancel")}
                      </Button>
                    </div>
                  </form>
                </div>
              )}
            </CardContent>
          </Card>

          <div className="text-center">
            <p className="text-muted-foreground text-sm">{__("invitations.footer.terms_agreement")}</p>
          </div>
        </div>
      </div>
    </>
  )
}
