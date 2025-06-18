import { Head, useForm } from "@inertiajs/react"
import { LoaderCircle, Mail } from "lucide-react"
import { FormEventHandler, useState } from "react"

import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { InputOTP, InputOTPGroup, InputOTPSlot } from "@/components/ui/input-otp"
import AuthLayout from "@/layouts/auth-layout"

export default function VerifyEmailOtp({ status }: { status?: string }) {
  const [otpValue, setOtpValue] = useState("")
  const { setData, post, processing, errors, reset } = useForm({
    otp: "",
  })

  const { post: resendPost, processing: resendProcessing } = useForm({})

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    post(route("verification.otp.store"), {
      onFinish: () => {
        reset("otp")
        setOtpValue("")
      },
    })
  }

  const resendOtp: FormEventHandler = (e) => {
    e.preventDefault()
    resendPost(route("verification.otp.resend"))
  }

  const handleOtpComplete = (value: string) => {
    setData("otp", value)
    // Auto-submit when OTP is complete
    if (value.length === 6) {
      setData("otp", value)
      setTimeout(() => {
        post(route("verification.otp.store"), {
          onFinish: () => {
            reset("otp")
            setOtpValue("")
          },
        })
      }, 100)
    }
  }

  const handleOtpChange = (value: string) => {
    setOtpValue(value)
    setData("otp", value)
  }

  return (
    <AuthLayout
      title="Verify your email"
      description="We've sent a 6-digit verification code to your email address. Please enter it below to verify your account."
    >
      <Head title="Email verification" />

      <div className="flex flex-col items-center space-y-6">
        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
          <Mail className="h-6 w-6" />
        </div>

        {status === "verification-code-sent" && (
          <div className="rounded-md bg-green-50 p-4 text-center">
            <div className="text-sm font-medium text-green-800">
              A new verification code has been sent to your email address.
            </div>
          </div>
        )}

        <div className="text-center">
          <p className="text-sm text-muted-foreground">
            Enter the 6-digit code sent to your email address
          </p>
        </div>

        <form onSubmit={submit} className="w-full space-y-6">
          <div className="flex flex-col items-center space-y-4">
            <InputOTP
              maxLength={6}
              value={otpValue}
              onChange={handleOtpChange}
              onComplete={handleOtpComplete}
              disabled={processing}
              autoFocus
            >
              <InputOTPGroup className="gap-2">
                <InputOTPSlot index={0} />
                <InputOTPSlot index={1} />
                <InputOTPSlot index={2} />
                <InputOTPSlot index={3} />
                <InputOTPSlot index={4} />
                <InputOTPSlot index={5} />
              </InputOTPGroup>
            </InputOTP>

            <InputError message={errors.otp} className="text-center" />
          </div>

          <div className="flex flex-col space-y-4">
            <Button type="submit" disabled={processing || otpValue.length !== 6} className="w-full">
              {processing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
              Verify Email
            </Button>

            <div className="text-center space-y-2">
              <p className="text-sm text-muted-foreground">
                Didn't receive the code?
              </p>
              <Button
                type="button"
                variant="ghost"
                onClick={resendOtp}
                disabled={resendProcessing}
                className="text-sm h-auto p-0 font-normal"
              >
                {resendProcessing && <LoaderCircle className="h-4 w-4 animate-spin mr-2" />}
                Resend verification code
              </Button>
            </div>
          </div>
        </form>

        <div className="text-center">
          <TextLink href={route("logout")} method="post" className="text-sm">
            Log out
          </TextLink>
        </div>
      </div>
    </AuthLayout>
  )
}
