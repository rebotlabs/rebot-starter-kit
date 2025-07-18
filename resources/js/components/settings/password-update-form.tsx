import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Transition } from "@headlessui/react"
import { useForm } from "@inertiajs/react"
import { FormEventHandler, useRef } from "react"

export default function PasswordUpdateForm() {
  const passwordInput = useRef<HTMLInputElement>(null)
  const currentPasswordInput = useRef<HTMLInputElement>(null)

  const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
  })

  const updatePassword: FormEventHandler = (e) => {
    e.preventDefault()

    put(route("settings.password.update"), {
      preserveScroll: true,
      onSuccess: () => reset(),
      onError: (errors) => {
        if (errors.password) {
          reset("password", "password_confirmation")
          passwordInput.current?.focus()
        }

        if (errors.current_password) {
          reset("current_password")
          currentPasswordInput.current?.focus()
        }
      },
    })
  }

  return (
    <form onSubmit={updatePassword}>
      <Card>
        <CardHeader>
          <CardTitle>Update password</CardTitle>
          <CardDescription>Ensure your account is using a long, random password to stay secure</CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <div className="grid gap-2">
            <Label htmlFor="current_password">Current password</Label>

            <Input
              id="current_password"
              ref={currentPasswordInput}
              value={data.current_password}
              onChange={(e) => setData("current_password", e.target.value)}
              type="password"
              className="mt-1 block w-full"
              autoComplete="current-password"
              placeholder="Current password"
            />

            <InputError message={errors.current_password} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="password">New password</Label>

            <Input
              id="password"
              ref={passwordInput}
              value={data.password}
              onChange={(e) => setData("password", e.target.value)}
              type="password"
              className="mt-1 block w-full"
              autoComplete="new-password"
              placeholder="New password"
            />

            <InputError message={errors.password} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="password_confirmation">Confirm password</Label>

            <Input
              id="password_confirmation"
              value={data.password_confirmation}
              onChange={(e) => setData("password_confirmation", e.target.value)}
              type="password"
              className="mt-1 block w-full"
              autoComplete="new-password"
              placeholder="Confirm password"
            />

            <InputError message={errors.password_confirmation} />
          </div>
        </CardContent>

        <CardFooter>
          <Transition
            show={recentlySuccessful}
            enter="transition ease-in-out"
            enterFrom="opacity-0"
            leave="transition ease-in-out"
            leaveTo="opacity-0"
          >
            <p className="text-sm text-neutral-600">Saved</p>
          </Transition>

          <Button disabled={processing}>Save password</Button>
        </CardFooter>
      </Card>
    </form>
  )
}
