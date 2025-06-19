import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import { FormEventHandler } from "react"

import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { InputOTP, InputOTPGroup, InputOTPSlot } from "@/components/ui/input-otp"
import { Label } from "@/components/ui/label"

type TwoFactorChallengeForm = {
  code: string
}

export function TwoFactorChallengeForm() {
  const { data, setData, post, processing, errors } = useForm<Required<TwoFactorChallengeForm>>({
    code: "",
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route("two-factor.login"))
  }

  const handleComplete = (value: string) => {
    setData("code", value)
    if (value.length === 6) {
      post(route("two-factor.login"))
    }
  }

  return (
    <>
      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-4 text-center">
            <div className="grid gap-2">
              <Label htmlFor="code">Authentication Code</Label>
              <p className="text-muted-foreground text-sm">Please enter the 6-digit code from your authenticator app.</p>
            </div>

            <div className="flex justify-center">
              <InputOTP
                maxLength={6}
                value={data.code}
                onChange={(value) => setData("code", value)}
                onComplete={handleComplete}
                disabled={processing}
              >
                <InputOTPGroup>
                  <InputOTPSlot index={0} />
                  <InputOTPSlot index={1} />
                  <InputOTPSlot index={2} />
                  <InputOTPSlot index={3} />
                  <InputOTPSlot index={4} />
                  <InputOTPSlot index={5} />
                </InputOTPGroup>
              </InputOTP>
            </div>

            <InputError message={errors.code} />
          </div>

          <Button type="submit" className="w-full" disabled={processing || data.code.length < 6}>
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            Verify Code
          </Button>
        </div>

        <div className="text-muted-foreground text-center text-sm">
          <TextLink href={route("login")}>← Back to login</TextLink>
        </div>
      </form>
    </>
  )
}
