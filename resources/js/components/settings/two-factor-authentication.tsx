import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Checkbox } from "@/components/ui/checkbox"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { InputOTP, InputOTPGroup, InputOTPSlot } from "@/components/ui/input-otp"
import { Label } from "@/components/ui/label"
import { useLang } from "@/hooks/useLang"
import { Check, Copy, Download, Printer, Shield, ShieldCheck } from "lucide-react"
import { useState } from "react"

interface Props {
  twoFactorEnabled: boolean
  recoveryCodes?: string[]
}

interface SetupResponse {
  qrCode: string
  secret: string
}

interface ConfirmResponse {
  recoveryCodes: string[]
}

export default function TwoFactorAuthentication({ twoFactorEnabled, recoveryCodes }: Props) {
  const { __ } = useLang()

  // Two-Factor Authentication state
  const [showSetupModal, setShowSetupModal] = useState(false)
  const [showDisableModal, setShowDisableModal] = useState(false)
  const [showRecoveryCodesModal, setShowRecoveryCodesModal] = useState(false)
  const [setupStep, setSetupStep] = useState<"qr" | "recovery">("qr")
  const [qrCode, setQrCode] = useState("")
  const [secret, setSecret] = useState("")
  const [confirmationCode, setConfirmationCode] = useState("")
  const [currentRecoveryCodes, setCurrentRecoveryCodes] = useState<string[]>([])
  const [codesSaved, setCodesSaved] = useState(false)
  const [disablePassword, setDisablePassword] = useState("")
  const [isLoading, setIsLoading] = useState(false)
  const [twoFactorError, setTwoFactorError] = useState("")
  const [copiedItems, setCopiedItems] = useState<Set<string>>(new Set())

  const enableTwoFactor = async () => {
    setIsLoading(true)
    setTwoFactorError("")

    try {
      const csrfToken =
        (window as { csrfToken?: string }).csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""

      const response = await fetch(route("settings.security.two-factor.store"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
      })

      if (!response.ok) {
        throw new Error("Failed to generate QR code")
      }

      const data: SetupResponse = await response.json()
      setQrCode(data.qrCode)
      setSecret(data.secret)
      setShowSetupModal(true)
      setSetupStep("qr")
    } catch {
      setTwoFactorError("Failed to set up two-factor authentication. Please try again.")
    } finally {
      setIsLoading(false)
    }
  }

  const confirmTwoFactor = async (code: string) => {
    if (!code || code.length !== 6) return

    setIsLoading(true)
    setTwoFactorError("")

    try {
      const csrfToken =
        (window as { csrfToken?: string }).csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""

      const response = await fetch(route("settings.security.two-factor.confirm"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ code }),
      })

      const data = await response.json()

      if (!response.ok) {
        setTwoFactorError(data.errors?.code?.[0] || "Invalid code. Please try again.")
        setConfirmationCode("")
        return
      }

      const confirmData: ConfirmResponse = data
      setCurrentRecoveryCodes(confirmData.recoveryCodes)
      setSetupStep("recovery")
      setCodesSaved(false)
    } catch {
      setTwoFactorError("Failed to confirm two-factor authentication. Please try again.")
      setConfirmationCode("")
    } finally {
      setIsLoading(false)
    }
  }

  const completeTwoFactorSetup = () => {
    setShowSetupModal(false)
    setConfirmationCode("")
    setCodesSaved(false)
    setSetupStep("qr")
    window.location.reload() // Refresh to update the UI state
  }

  const disableTwoFactor = async () => {
    if (!disablePassword) return

    setIsLoading(true)
    setTwoFactorError("")

    try {
      const csrfToken =
        (window as { csrfToken?: string }).csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""

      const response = await fetch(route("settings.security.two-factor.destroy"), {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ password: disablePassword }),
      })

      const data = await response.json()

      if (!response.ok) {
        setTwoFactorError(data.errors?.password?.[0] || "Failed to disable two-factor authentication.")
        return
      }

      setShowDisableModal(false)
      setDisablePassword("")
      window.location.reload() // Refresh to update the UI state
    } catch {
      setTwoFactorError("Failed to disable two-factor authentication. Please try again.")
    } finally {
      setIsLoading(false)
    }
  }

  const copyToClipboard = async (text: string, type: string) => {
    try {
      await navigator.clipboard.writeText(text)
      setCopiedItems((prev) => new Set(prev).add(type))
      setTimeout(() => {
        setCopiedItems((prev) => {
          const newSet = new Set(prev)
          newSet.delete(type)
          return newSet
        })
      }, 2000)
    } catch (error) {
      console.error("Failed to copy to clipboard:", error)
    }
  }

  const downloadRecoveryCodes = () => {
    const content = `Recovery Codes for ${document.title}\n\n${currentRecoveryCodes.join("\n")}\n\nKeep these codes safe and secure. They can be used to recover access to your account if your two-factor authentication device is lost.`
    const blob = new Blob([content], { type: "text/plain" })
    const url = URL.createObjectURL(blob)
    const a = document.createElement("a")
    a.href = url
    a.download = "recovery-codes.txt"
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }

  const printRecoveryCodes = () => {
    const content = `
      <html>
        <head><title>Recovery Codes</title></head>
        <body>
          <h1>Recovery Codes for ${document.title}</h1>
          <p>Keep these codes safe and secure. They can be used to recover access to your account if your two-factor authentication device is lost.</p>
          <ul>
            ${currentRecoveryCodes.map((code) => `<li>${code}</li>`).join("")}
          </ul>
        </body>
      </html>
    `
    const printWindow = window.open("", "_blank")
    if (printWindow) {
      printWindow.document.write(content)
      printWindow.document.close()
      printWindow.print()
    }
  }

  return (
    <>
      {/* Two-Factor Authentication Section */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            {twoFactorEnabled ? <ShieldCheck className="h-5 w-5 text-green-600" /> : <Shield className="h-5 w-5" />}
            {__("ui.two_factor.title")}
          </CardTitle>
          <CardDescription>{__("ui.two_factor.description")}</CardDescription>
        </CardHeader>

        <CardContent className="space-y-4">
          <div className="flex items-center justify-between">
            <div className="space-y-1">
              <p className="text-sm">{twoFactorEnabled ? __("ui.two_factor.enabled") : __("ui.two_factor.not_enabled")}</p>
              {twoFactorEnabled && (
                <Badge variant="secondary" className="bg-green-50 text-green-700">
                  {__("ui.status.active")}
                </Badge>
              )}
            </div>

            <div className="flex gap-2">
              {twoFactorEnabled ? (
                <>
                  <Button variant="outline" onClick={() => setShowRecoveryCodesModal(true)}>
                    View Recovery Codes
                  </Button>
                  <Button variant="destructive" onClick={() => setShowDisableModal(true)} disabled={isLoading}>
                    {__("ui.two_factor.disable")}
                  </Button>
                </>
              ) : (
                <Button onClick={enableTwoFactor} disabled={isLoading}>
                  {isLoading ? __("ui.messages.loading") : __("ui.two_factor.enable")}
                </Button>
              )}
            </div>
          </div>

          {twoFactorError && <div className="rounded-md bg-red-50 p-3 text-sm text-red-600">{twoFactorError}</div>}
        </CardContent>
      </Card>

      {/* Two-Factor Setup Modal */}
      <Dialog open={showSetupModal} onOpenChange={setShowSetupModal}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>{__("ui.two_factor.setup_title")}</DialogTitle>
            <DialogDescription>
              {setupStep === "qr" ? __("ui.two_factor.setup_description") : __("ui.two_factor.recovery_codes_description")}
            </DialogDescription>
          </DialogHeader>

          {setupStep === "qr" ? (
            <div className="space-y-4">
              {qrCode && (
                <div className="flex flex-col items-center space-y-4">
                  <div className="rounded-lg border bg-white p-4" dangerouslySetInnerHTML={{ __html: qrCode }} />

                  <div className="w-full space-y-2">
                    <Label htmlFor="secret">{__("ui.two_factor.secret_key")}</Label>
                    <div className="flex gap-2">
                      <Input id="secret" value={secret} readOnly className="font-mono text-sm" />
                      <Button variant="outline" size="sm" onClick={() => copyToClipboard(secret, "secret")}>
                        {copiedItems.has("secret") ? <Check className="h-4 w-4" /> : <Copy className="h-4 w-4" />}
                      </Button>
                    </div>
                  </div>
                </div>
              )}

              <div className="space-y-2">
                <Label htmlFor="confirmation_code">{__("ui.two_factor.enter_code")}</Label>
                <div className="flex justify-center">
                  <InputOTP
                    maxLength={6}
                    value={confirmationCode}
                    onChange={(value) => setConfirmationCode(value)}
                    onComplete={(value) => confirmTwoFactor(value)}
                    disabled={isLoading}
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
                {isLoading && <div className="text-muted-foreground text-center text-sm">{__("ui.messages.loading")}</div>}
              </div>

              {twoFactorError && <div className="rounded-md bg-red-50 p-3 text-sm text-red-600">{twoFactorError}</div>}

              <div className="flex justify-end gap-2">
                <Button variant="outline" onClick={() => setShowSetupModal(false)}>
                  {__("ui.buttons.cancel")}
                </Button>
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              <div className="rounded-lg border p-4">
                <h4 className="text-primary mb-2 font-medium">{__("ui.two_factor.recovery_codes_title")}</h4>
                <div className="grid grid-cols-2 gap-2 font-mono text-sm">
                  {currentRecoveryCodes.map((code, index) => (
                    <div key={index} className="rounded border p-2 text-center">
                      {code}
                    </div>
                  ))}
                </div>
              </div>

              <div className="flex gap-2">
                <Button variant="outline" size="sm" onClick={() => copyToClipboard(currentRecoveryCodes.join("\n"), "codes")} className="flex-1">
                  {copiedItems.has("codes") ? <Check className="mr-2 h-4 w-4" /> : <Copy className="mr-2 h-4 w-4" />}
                  {__("ui.two_factor.copy")}
                </Button>
                <Button variant="outline" size="sm" onClick={downloadRecoveryCodes} className="flex-1">
                  <Download className="mr-2 h-4 w-4" />
                  {__("ui.two_factor.download")}
                </Button>
                <Button variant="outline" size="sm" onClick={printRecoveryCodes} className="flex-1">
                  <Printer className="mr-2 h-4 w-4" />
                  {__("ui.two_factor.print")}
                </Button>
              </div>

              <div className="flex items-center space-x-2">
                <Checkbox id="codes-saved" checked={codesSaved} onCheckedChange={(checked) => setCodesSaved(checked as boolean)} />
                <Label htmlFor="codes-saved" className="text-sm">
                  {__("ui.two_factor.recovery_codes_saved")}
                </Label>
              </div>

              <div className="flex justify-end">
                <Button onClick={completeTwoFactorSetup} disabled={!codesSaved}>
                  {__("ui.buttons.close")}
                </Button>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Disable Two-Factor Modal */}
      <Dialog open={showDisableModal} onOpenChange={setShowDisableModal}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>{__("ui.two_factor.disable_title")}</DialogTitle>
            <DialogDescription>
              {__("ui.two_factor.disable_description")}
              <br />
              <span className="font-medium text-red-600">{__("ui.two_factor.disable_warning")}</span>
            </DialogDescription>
          </DialogHeader>

          <div className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="disable_password">Password</Label>
              <Input
                id="disable_password"
                type="password"
                value={disablePassword}
                onChange={(e) => setDisablePassword(e.target.value)}
                placeholder="Enter your password"
              />
            </div>

            {twoFactorError && <div className="rounded-md bg-red-50 p-3 text-sm text-red-600">{twoFactorError}</div>}

            <div className="flex justify-end gap-2">
              <Button variant="outline" onClick={() => setShowDisableModal(false)}>
                {__("ui.buttons.cancel")}
              </Button>
              <Button variant="destructive" onClick={disableTwoFactor} disabled={!disablePassword || isLoading}>
                {isLoading ? __("ui.messages.loading") : __("ui.two_factor.disable")}
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      {/* Recovery Codes Modal */}
      <Dialog open={showRecoveryCodesModal} onOpenChange={setShowRecoveryCodesModal}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>{__("ui.two_factor.recovery_codes_title")}</DialogTitle>
            <DialogDescription>{__("ui.two_factor.recovery_codes_description")}</DialogDescription>
          </DialogHeader>

          <div className="space-y-4">
            {recoveryCodes && (
              <div className="rounded-lg border bg-gray-50 p-4">
                <div className="grid grid-cols-2 gap-2 font-mono text-sm">
                  {recoveryCodes.map((code, index) => (
                    <div key={index} className="rounded border bg-white p-2 text-center">
                      {code}
                    </div>
                  ))}
                </div>
              </div>
            )}

            <div className="flex gap-2">
              <Button
                variant="outline"
                size="sm"
                onClick={() => recoveryCodes && copyToClipboard(recoveryCodes.join("\n"), "existing-codes")}
                className="flex-1"
              >
                {copiedItems.has("existing-codes") ? <Check className="mr-2 h-4 w-4" /> : <Copy className="mr-2 h-4 w-4" />}
                {__("ui.two_factor.copy")}
              </Button>
              <Button
                variant="outline"
                size="sm"
                onClick={() => {
                  if (recoveryCodes) {
                    setCurrentRecoveryCodes(recoveryCodes)
                    downloadRecoveryCodes()
                  }
                }}
                className="flex-1"
              >
                <Download className="mr-2 h-4 w-4" />
                {__("ui.two_factor.download")}
              </Button>
            </div>

            <Button
              variant="outline"
              className="w-full"
              onClick={async () => {
                try {
                  const csrfToken =
                    (window as { csrfToken?: string }).csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""

                  const response = await fetch(route("settings.security.recovery-codes"), {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/json",
                      "X-CSRF-TOKEN": csrfToken,
                    },
                  })

                  if (response.ok) {
                    window.location.reload()
                  }
                } catch (error) {
                  console.error("Failed to regenerate recovery codes:", error)
                }
              }}
            >
              {__("ui.two_factor.regenerate_recovery_codes")}
            </Button>

            <div className="flex justify-end">
              <Button onClick={() => setShowRecoveryCodesModal(false)}>{__("ui.buttons.close")}</Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </>
  )
}
