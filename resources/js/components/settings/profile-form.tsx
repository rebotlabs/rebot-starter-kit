import { type SharedData } from "@/types"
import { Transition } from "@headlessui/react"
import { Link, useForm, usePage } from "@inertiajs/react"
import { FormEventHandler } from "react"

import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useLang } from "@/hooks/useLang"

type ProfileForm = {
  name: string
  email: string
}

interface ProfileFormProps {
  mustVerifyEmail: boolean
  status?: string
}

export function ProfileForm({ mustVerifyEmail, status }: ProfileFormProps) {
  const { __ } = useLang()
  const { auth } = usePage<SharedData>().props

  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Required<ProfileForm>>({
    name: auth.user.name,
    email: auth.user.email,
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    patch(route("settings.profile.update"), {
      preserveScroll: true,
    })
  }

  return (
    <form onSubmit={submit}>
      <Card>
        <CardHeader>
          <CardTitle>{__("settings.profile.title")}</CardTitle>
          <CardDescription>{__("settings.profile.description")}</CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <div className="grid gap-2">
            <Label htmlFor="name">{__("settings.profile.name_label")}</Label>

            <Input
              id="name"
              className="mt-1 block w-full"
              value={data.name}
              onChange={(e) => setData("name", e.target.value)}
              required
              autoComplete="name"
              placeholder={__("settings.profile.name_placeholder")}
            />

            <InputError className="mt-2" message={errors.name} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="email">{__("settings.profile.email_label")}</Label>

            <Input
              id="email"
              type="email"
              className="mt-1 block w-full"
              value={data.email}
              onChange={(e) => setData("email", e.target.value)}
              required
              autoComplete="username"
              placeholder={__("settings.profile.email_placeholder")}
            />

            <InputError className="mt-2" message={errors.email} />
          </div>

          {mustVerifyEmail && auth.user.email_verified_at === null && (
            <div>
              <p className="text-muted-foreground -mt-4 text-sm">
                {__("settings.profile.email_unverified")}{" "}
                <Link
                  href={route("verification.send")}
                  method="post"
                  as="button"
                  className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                >
                  {__("auth.buttons.verify_email")}
                </Link>
              </p>

              {status === "verification-link-sent" && (
                <div className="mt-2 text-sm font-medium text-green-600">{__("settings.profile.email_verification_sent")}</div>
              )}
            </div>
          )}
        </CardContent>
        <CardFooter>
          <Transition
            show={recentlySuccessful}
            enter="transition ease-in-out"
            enterFrom="opacity-0"
            leave="transition ease-in-out"
            leaveTo="opacity-0"
          >
            <p className="text-muted-foreground text-sm">{__("ui.buttons.save")}</p>
          </Transition>

          <Button disabled={processing}>{__("settings.profile.save_changes")}</Button>
        </CardFooter>
      </Card>
    </form>
  )
}
