import InputError from "@/components/input-error"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { ImageUpload } from "@/components/ui/image-upload"
import { useTranslation } from "@/hooks/use-i18n"
import type { Organization } from "@/types"
import { Transition } from "@headlessui/react"
import { router, usePage } from "@inertiajs/react"
import { useState } from "react"

export function LogoUpload() {
  const t = useTranslation()
  const { organization } = usePage<{ organization: Organization }>().props
  const [isUploading, setIsUploading] = useState(false)
  const [isRemoving, setIsRemoving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [recentlySuccessful, setRecentlySuccessful] = useState(false)

  const handleImageSelect = (file: File) => {
    setError(null)
    setIsUploading(true)

    const formData = new FormData()
    formData.append("logo", file)

    router.post(route("organization.settings.logo.store", [organization]), formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        setIsUploading(false)
        setRecentlySuccessful(true)
        setTimeout(() => setRecentlySuccessful(false), 3000)
      },
      onError: (errors) => {
        setIsUploading(false)
        setError(errors.logo || t("ui.logo.validation.upload_failed"))
      },
    })
  }

  const handleImageRemove = () => {
    setError(null)
    setIsRemoving(true)

    router.delete(route("organization.settings.logo.destroy", [organization]), {
      preserveScroll: true,
      onSuccess: () => {
        setIsRemoving(false)
        setRecentlySuccessful(true)
        setTimeout(() => setRecentlySuccessful(false), 3000)
      },
      onError: () => {
        setIsRemoving(false)
        setError(t("ui.logo.validation.delete_failed"))
      },
    })
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{t("ui.logo.title")}</CardTitle>
        <CardDescription>{t("ui.logo.description")}</CardDescription>
      </CardHeader>

      <CardContent className="space-y-6">
        <div className="flex justify-center">
          <ImageUpload
            currentImage={organization.logo}
            fallbackText={organization.name}
            onImageSelect={handleImageSelect}
            onImageRemove={handleImageRemove}
            isUploading={isUploading}
            isRemoving={isRemoving}
            size="xl"
            shape="square"
            uploadText={t("ui.logo.upload")}
            changeText={t("ui.logo.change")}
            removeText={t("ui.logo.remove")}
          />
        </div>

        {error && <InputError message={error} />}

        <Transition show={recentlySuccessful} enter="transition ease-in-out" enterFrom="opacity-0" leave="transition ease-in-out" leaveTo="opacity-0">
          <p className="text-muted-foreground text-center text-sm">{organization.logo ? t("ui.logo.upload_success") : t("ui.logo.delete_success")}</p>
        </Transition>
      </CardContent>
    </Card>
  )
}
