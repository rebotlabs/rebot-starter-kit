import InputError from "@/components/input-error"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { ImageUpload } from "@/components/ui/image-upload"
import { useTranslation } from "@/hooks/use-i18n"
import type { SharedData } from "@/types"
import { Transition } from "@headlessui/react"
import { router, usePage } from "@inertiajs/react"
import { useState } from "react"

export function AvatarUpload() {
  const t = useTranslation()
  const { auth } = usePage<SharedData>().props
  const [isUploading, setIsUploading] = useState(false)
  const [isRemoving, setIsRemoving] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [recentlySuccessful, setRecentlySuccessful] = useState(false)

  const handleImageSelect = (file: File) => {
    setError(null)
    setIsUploading(true)

    const formData = new FormData()
    formData.append("avatar", file)

    router.post(route("settings.avatar.store"), formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        setIsUploading(false)
        setRecentlySuccessful(true)
        setTimeout(() => setRecentlySuccessful(false), 3000)
      },
      onError: (errors) => {
        setIsUploading(false)
        setError(errors.avatar || t("ui.avatar.validation.upload_failed"))
      },
    })
  }

  const handleImageRemove = () => {
    setError(null)
    setIsRemoving(true)

    router.delete(route("settings.avatar.destroy"), {
      preserveScroll: true,
      onSuccess: () => {
        setIsRemoving(false)
        setRecentlySuccessful(true)
        setTimeout(() => setRecentlySuccessful(false), 3000)
      },
      onError: () => {
        setIsRemoving(false)
        setError(t("ui.avatar.validation.delete_failed"))
      },
    })
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{t("ui.avatar.title")}</CardTitle>
        <CardDescription>{t("ui.avatar.description")}</CardDescription>
      </CardHeader>

      <CardContent className="space-y-6">
        <div className="flex justify-center">
          <ImageUpload
            currentImage={auth.user.avatar}
            fallbackText={auth.user.name}
            onImageSelect={handleImageSelect}
            onImageRemove={handleImageRemove}
            isUploading={isUploading}
            isRemoving={isRemoving}
            size="xl"
            uploadText={t("ui.avatar.upload")}
            changeText={t("ui.avatar.change")}
            removeText={t("ui.avatar.remove")}
          />
        </div>

        {error && <InputError message={error} />}

        <Transition show={recentlySuccessful} enter="transition ease-in-out" enterFrom="opacity-0" leave="transition ease-in-out" leaveTo="opacity-0">
          <p className="text-muted-foreground text-center text-sm">
            {auth.user.avatar ? t("ui.avatar.upload_success") : t("ui.avatar.delete_success")}
          </p>
        </Transition>
      </CardContent>
    </Card>
  )
}
