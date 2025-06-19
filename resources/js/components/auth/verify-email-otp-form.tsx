import { useForm } from "@inertiajs/react"
import { LoaderCircle, Mail } from "lucide-react"
import { FormEventHandler, useState } from "react"

import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { InputOTP, InputOTPGroup, InputOTPSlot } from "@/components/ui/input-otp"
import { useLang } from "@/hooks/useLang"

interface VerifyEmailOtpFormProps {
  status?: string
}

export function VerifyEmailOtpForm({ status }: VerifyEmailOtpFormProps) {
  const { __ } = useLang()
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
    <div className="flex flex-col items-center space-y-6">
      <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
        <Mail className="h-6 w-6" />
      </div>

      {status === "verification-code-sent" && (
        <div className="rounded-md bg-green-50 p-4 text-center">
          <div className="text-sm font-medium text-green-800">{__("ui.verification.code_sent")}</div>
        </div>
      )}

      <div className="text-center">
        <p className="text-muted-foreground text-sm">{__("ui.verification.enter_code")}</p>
      </div>

      <form onSubmit={submit} className="w-full space-y-6">
        <div className="flex flex-col items-center space-y-4">
          <InputOTP maxLength={6} value={otpValue} onChange={handleOtpChange} onComplete={handleOtpComplete} disabled={processing} autoFocus>
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
            {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
            {__("ui.verification.verify_email")}
          </Button>

          <div className="space-y-2 text-center">
            <p className="text-muted-foreground text-sm">{__("ui.verification.didnt_receive")}</p>
            <Button type="button" variant="ghost" onClick={resendOtp} disabled={resendProcessing} className="h-auto p-0 text-sm font-normal">
              {resendProcessing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
              {__("ui.verification.resend_code")}
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
  )
}
